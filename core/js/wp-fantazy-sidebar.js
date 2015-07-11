console.log('Fantazy Sidebar is loaded');
fs = {};
if (typeof console === 'undefined') {
    console = {
        log: function() {},
        dir: function() {}
    };
}
(function() {
    fs.start = function(opts) {
        jQuery(function() {
            opts = jQuery.extend({}, {
                contentID: '#content',
                sidebarID: '#sidebar',
                contentClass:'.content',
                sidebarClass:'.sidebar',
                waitingTime: 3000,
                debounce: 500,
                animate: 3000,
                offsetTop: 0,
                offsetBottom: 0,
                minHDiff: 0
            }, opts);
            (function ($) {
               $(document);
                    $(jQuery(opts.contentClass)).attr("id", "content");
                    $(jQuery(opts.sidebarClass)).attr("id", "sidebar");
            }(jQuery));
            setTimeout(function() {

                var $w  = jQuery(window),
                    $c  = jQuery(opts.contentID),
                    $ss = jQuery(opts.sidebarID),
                    $b  = jQuery('body');

                console.dir(opts);

                if ($c.length && $ss.length) {
                    $ss.each(function() {
                        (function($s) {
                            // console.log($c.height() - $s.height());
                            if ($c.height() - $s.height() > opts.minHDiff || opts.dynamicTop) {
                                $s.parent().css('position', 'relative');
                                var initialSPos = $s.position(),
                                    initialSOff = $s.offset();

                                setTimeout(function() {
                                    $s.css({
                                        position: 'absolute',
                                        left: initialSPos.left + 'px',
                                        top: initialSPos.top + 'px'
                                    }).find('.widget').css('position', 'relative');

                                    var lastScrollY = -1,
                                        sidebarIDTop = initialSPos.top,
                                        offsetTop = initialSOff.top - sidebarIDTop,
                                        maxTop = sidebarIDTop + $c.height() - $s.outerHeight(),
                                        onScroll = function(e) {
                                            var scrollY = $w.scrollTop(),
                                                t, scrollingDown = scrollY > lastScrollY;

                                            if ((scrollingDown && scrollY > sidebarIDTop + offsetTop && scrollY + $w.height() > $s.position().top + $s.height() + offsetTop - sidebarIDTop) || (!scrollingDown && scrollY < $s.position().top + offsetTop)) {
                                                if (e.type === 'scroll' && ($w.height() > $s.height() || !scrollingDown)) {

                                                    t = Math.max(sidebarIDTop, scrollY - (offsetTop) + (~~opts.offsetTop));
                                                } else {

                                                    t = Math.max(sidebarIDTop, scrollY + $w.height() - $s.outerHeight() - offsetTop - (~~opts.offsetBottom));
                                                }

                                                t = Math.min(t, opts.dynamicTop ? (sidebarIDTop + $c.height() - $s.outerHeight()) : maxTop);
                                                $s.stop().animate({
                                                    top: t + 'px'
                                                }, ~~opts.animate);
                                            }
                                            lastScrollY = scrollY;
                                        };
                                    if (opts.debounce && Function.prototype.debounce) {
                                        onScroll = onScroll.debounce(opts.debounce);
                                    }

                                    $w.scroll(onScroll).resize(onScroll);
                                    onScroll({
                                        type: 'scroll'
                                    });

                                    $w.scroll(function() {
                                        $s.stop();
                                    });
                                }, 0);

                            }
                        })(jQuery(this));
                    });

                } else {
                    if ($c.length === 0) {
                        console.log(opts.contentID + ' not found');
                    }
                    if ($ss.length === 0) {
                        console.log(opts.sidebarID + ' not found');
                    }
                }

            }, opts.waitingTime);
        });
    };
})();