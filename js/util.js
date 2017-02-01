var Util = {
    
    colorTable: '#EEEEEE',
    colorOut: '#FFFFFF',
    colorObstacle: '#222222',
    colorMixed: '#880088',
    colorSuggest: '#FFFF99',
    colorBlink: '#FFCC00',
    
    blinkBoldId: null,
    blinkBoldElement: null,
    
    showBox: function(event, box) {
        if (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
        this.hideMessage('table');
        Util.showDirsBox();
        Util.hideDirs();
        Util.hideDemoNotes();
        $('.slide-box').not('.' + box + '-box').hide();
        $('.' + box + '-box').show();
    },
    sortNumber: function(a, b) {
        return a - b;
    },
    /*
    showMessage: function(message) {
        $('#message').html(message).show();
    },
    hideMessage: function() {
        $('#message').html('').hide();
    },
    */        
    showMessage: function(box, message) {
        $('.message').filter('.' + box).html(message).show();
    },
    hideMessage: function(box) {
        $('.message').filter('.' + box).html('').hide();
    },
    
    clearTable: function() {
        var fieldColor;
        for (var i = 1; i < 13; i++) {
            for (var j = 1; j < 6; j++) {
                if (i < 3 || i > 10) {
                    fieldColor = this.colorOut;
                } else {
                    fieldColor = this.colorTable;
                }
                this.setFieldColor('' + i + j + '00', fieldColor);
                this.setFieldColor('' + i + j + '01', fieldColor);
                this.setFieldColor('' + i + j + '10', fieldColor);
                this.setFieldColor('' + i + j + '11', fieldColor);
            }
        }
    },
    showSelectButton: function() {
        $('.select-button').css('visibility', 'visible');
    },
    hideSelectButton: function() {
        $('.select-button').css('visibility', 'hidden');
    },
    showQuitButton: function() {
        $('.quit-button').css('display', 'inline-block');
    },
    hideQuitButton: function() {
        $('.quit-button').hide();
    },
    setFieldColor: function(id, color) {
        var field = document.getElementById('' + id);
        field.style.fill = color;
    },
    setGroupColor: function(group, color) {
        for (var i = 0; i < group.length; i++) {
            this.setFieldColor(group[i], color);
        }
    },
    
    showPlayDemoLink: function() {
        $('.play-demo').show();
    },
    hidePlayDemoLink: function() {
        $('.play-demo').hide();
    },
    
    scroll: function(nav, initial) {
        if (!initial) {
            Util.hideMessage('table');
        }
        $(document).off('scroll');
        $('html, body').stop().animate(
            {scrollTop: $('#' + nav).offset().top - 40},
            0,
            'swing',
            function () {
                $(document).on('scroll', Util.onScroll);
            }
        );
    },
    
    
    nav: null,
    body: null,
    window: null,
    navOffsetTop: 0,
    
    initScroll: function() {
        this.nav = $('.navbar');
        this.body = $('body');
        this.window = $(window);
        this.navOffsetTop = this.nav.offset().top;
        
        this.window.on('scroll', this.onScroll);
        this.window.on('resize', this.resize);
        $('a.anchor').on('click', this.smoothScroll);
    },
    
    smoothScroll: function(e) {
        e.preventDefault();
        Util.hideMessage('table');
        $(document).off('scroll');
        
        $('html, body').stop().animate(
            {scrollTop: $(this.hash).offset().top - 40},
            0,
            'swing',
            function () {
                $(document).on('scroll', Util.onScroll);
            }
        );
    },
    
    resize: function() {
        Util.body.removeClass('has-docked-nav');
        Util.navOffsetTop = Util.nav.offset().top;
        Util.onScroll();
    },
    
    onScroll: function() {
        if (Util.navOffsetTop < Util.window.scrollTop() && !Util.body.hasClass('has-docked-nav')) {
            Util.body.addClass('has-docked-nav');
        }
        if (Util.navOffsetTop > Util.window.scrollTop() && Util.body.hasClass('has-docked-nav')) {
            Util.body.removeClass('has-docked-nav');
        }
    },
    
    showSection: function(event) {
        event.preventDefault();
        var $link = $(event.target);
        $($link.attr('href')).removeClass('hidden');
        $link.addClass('hidden');
    },
    showDir: function(dir, content) {
        Util.hideDirs();
        if (content === undefined) {
            $('.dirs').find('.dir.' + dir).show();
        } else {
            $('.dirs').find('.dir.' + dir).html(content).show();
        }
    },
    hideDirs: function() {
        $('.dirs').find('.dir').hide();
    },
    showDirsBox: function() {
        $('.dirs').show();
    },
    hideDirsBox: function() {
        $('.dirs').hide();
    },
    showDemoNotes: function() {
        $('.demo-notes').show();
    },
    hideDemoNotes: function() {
        $('.demo-notes').hide();
    },
    
    blinkBold: function($element, count) {
        Util.blinkBoldElement = $element;
        Util.blinkBoldElement.css('font-weight', 'bold');
        var value = 'normal';
        var counter = 0;
        count = (count - 1) * 2;
        Util.blinkBoldId = setInterval(function() {
            Util.blinkBoldElement.css('font-weight', value);
            value = value == 'bold' ? 'normal' : 'bold';
            if (counter > count) {
                Util.stopBlinkBold();
            }
            counter++;
        }, 600);
    },
    
    stopBlinkBold: function() {
        if (Util.blinkBoldId) {
            clearInterval(Util.blinkBoldId);
            Util.blinkBoldElement.css('font-weight', 'normal');
            Util.blinkBoldElement = null;
            Util.blinkBoldId = null;
        }
    }
    
};