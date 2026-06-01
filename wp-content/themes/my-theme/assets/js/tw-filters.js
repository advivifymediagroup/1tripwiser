/**
 * tw-filters.js — AJAX section filter for 1TRIPWISER
 *
 * Intercepts clicks on .tw-filter-pill inside .tw-filter-bar[data-section].
 * Fetches card HTML via admin-ajax, swaps only the grid, updates the URL
 * via history.pushState — zero page reload.
 */
(function () {
    'use strict';

    if ( typeof window.tw_ajax === 'undefined' ) { return; }

    var GRID_IDS = {
        packages:     'tw-cards-packages',
        itineraries:  'tw-cards-itineraries',
    };

    /* ── helpers ── */
    function getGrid( section ) {
        return document.getElementById( GRID_IDS[ section ] );
    }

    function setLoading( grid, on ) {
        grid.style.transition  = 'opacity 0.2s';
        grid.style.opacity     = on ? '0.35' : '1';
        grid.style.pointerEvents = on ? 'none' : '';
    }

    function activatePill( bar, filterValue, param ) {
        bar.querySelectorAll( '.tw-filter-pill' ).forEach( function ( p ) {
            var href = p.getAttribute( 'href' ) || '';
            try {
                var u = new URL( href, window.location.href );
                var v = u.searchParams.get( param ) || 'all';
                p.classList.toggle( 'active', v === filterValue );
            } catch (e) { /* noop */ }
        });
    }

    function fetchGrid( section, filter, grid ) {
        setLoading( grid, true );

        var body = 'action=tw_filter_section'
            + '&nonce='   + encodeURIComponent( window.tw_ajax.nonce )
            + '&section=' + encodeURIComponent( section )
            + '&filter='  + encodeURIComponent( filter );

        fetch( window.tw_ajax.ajax_url, {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    body,
        } )
        .then( function ( r ) { return r.json(); } )
        .then( function ( data ) {
            if ( data && data.success && typeof data.data.html === 'string' ) {
                // Fade out → swap → fade in
                grid.style.opacity = '0';
                setTimeout( function () {
                    grid.innerHTML = data.data.html;
                    setLoading( grid, false );
                    // Small scroll hint so user sees the result without jumping
                    grid.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
                }, 180 );
            } else {
                setLoading( grid, false );
            }
        } )
        .catch( function () { setLoading( grid, false ); } );
    }

    /* ── click handler ── */
    document.addEventListener( 'click', function ( e ) {
        var pill = e.target.closest( '.tw-filter-pill' );
        if ( ! pill ) { return; }

        var bar = pill.closest( '.tw-filter-bar[data-section]' );
        if ( ! bar ) { return; }

        var section = bar.getAttribute( 'data-section' );
        var param   = bar.getAttribute( 'data-param' );
        var grid    = getGrid( section );
        if ( ! grid ) { return; }

        e.preventDefault();

        /* resolve filter value from the pill's href */
        var filterValue = 'all';
        try {
            var u = new URL( pill.getAttribute( 'href' ) || '', window.location.href );
            filterValue = u.searchParams.get( param ) || 'all';
        } catch (e2) { /* noop */ }

        /* optimistic UI — activate pill immediately */
        activatePill( bar, filterValue, param );

        /* push clean URL (no full reload) */
        try {
            var newUrl = new URL( window.location.href );
            if ( filterValue === 'all' ) {
                newUrl.searchParams.delete( param );
            } else {
                newUrl.searchParams.set( param, filterValue );
            }
            /* keep anchor hash */
            var anchor = section === 'packages' ? '#featured-packages' : '#upcoming-trips';
            history.pushState(
                { section: section, filter: filterValue, param: param },
                '',
                newUrl.pathname + ( newUrl.search || '' ) + anchor
            );
        } catch (e3) { /* noop */ }

        fetchGrid( section, filterValue, grid );
    } );

    /* ── back / forward support ── */
    window.addEventListener( 'popstate', function ( e ) {
        var state = e.state;
        if ( ! state || ! state.section ) { return; }

        var bar  = document.querySelector( '.tw-filter-bar[data-section="' + state.section + '"]' );
        var grid = getGrid( state.section );
        if ( ! bar || ! grid ) { return; }

        activatePill( bar, state.filter || 'all', state.param );
        fetchGrid( state.section, state.filter || 'all', grid );
    } );

}());
