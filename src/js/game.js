var Game = {
    
    FIRST_MOVE_TIME: 60,
    MOVE_TIME: 30,
    
    players: {
        a: [],
        b: []
    },
    passed: {
        a: [],
        b: []
    },
    active: 'a',
    nonActive: 'b',
    colors: {a: '#880000', b: '#000088'},
    activeColors: {a: '#FF0000', b: '#0000FF'},
    
    selectedFields: [],
    obstacles: [],
    
    selected: {a: false, b: false},
    
    moves: {a: 0, b: 0},
    movesHtml: {a: null, b: null},
    
    timer: null,
    timerHtml: null,
    timerId: null,
    
    suggestFields: [],
    suggestGroups: [],
    ambiguous: [],
    
    demo: false,
    level: 0,
    
    points: 0,
    maxPoints: 0,
    pointsHtml: null,
    
    play: function() {
        
        Util.hideMessage('table');
        
        var level = $('#level').val();
        if (!level) {
            return false;
        }
        var clientTime = new Date();
        var that = this;
        $.ajax({
            url: 'index.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_game',
                level: level,
                timezoneOffset: clientTime.getTimezoneOffset()
            },
            success: function(response) {
                if (response) {
                    if (response.obstacles && response.obstacles.length > 0) {
                        that.obstacles = response.obstacles;
                        that.minMoves = response.min_moves;
                        that.step = response.step;
                        that.level = level;
                        that.maxPoints = level * 3;
                        that.points = level * 3;
                        that.startGame();
                    }
                    if (response.message) {
                        Util.showMessage('table', response.message);
                    } else {
                        Util.hideMessage('table');
                    }
                }
            }
        });
        
    },
    
    quit: function(message) {
        this.unbindEvents('click systemClick');
        this.stopTimer();
        //this.unsetGameSession();
        $.ajax({
            url: 'index.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'quit'
            },
            success: function(response) {
                if (response) {
                    if (!response.error) {
                        Game.highlightPoints(response.points);
                        Util.hideQuitButton();
                        Util.showPlayDemoLink();
                    }
                    if (response.saved) {
                        Standings.refresh();
                        Played.refresh();
                    }
                    if (message) {
                        Util.showDir('end', message);
                        Game.blinkBold($('.dir.end'), 2);
                    } else if (response.message) {
                        Util.showDir('end', response.message);
                        Game.blinkBold($('.dir.end'), 2);
                    }
                }
            }
        });
    },
    /*  
    unsetGameSession: function() {
        $.post('index.php?action=unset_game_session');
    },
    */
    startGame: function() {
        Util.clearTable();
        this.resetVars();
        this.resetHtmlMoves();
        this.setPoints();
        this.setObstacles();
        Util.showSelectButton();
        Util.showQuitButton();
        Util.showBox(false, 'game');
        Util.hidePlayDemoLink();
        this.bindEvents('click systemClick');
        this.startTimer(this.FIRST_MOVE_TIME);
    },
    
    resetVars: function() {
        this.players = {
            a: [],
            b: []
        };
        this.passed = {
            a: [],
            b: []
        };
        this.active = 'a';
        this.nonActive = 'b';
        
        this.selectedFields = [];
        
        this.selected = {a: false, b: false};
        
        this.moves = {a: 0, b: 0};
        
        this.timerHtml = document.getElementById('timer');
        this.pointsHtml = document.getElementById('points');
        this.pointsHtml.style['border-bottom'] = 'none';
        
        this.suggestFields = [];
        this.suggestGroups = [];
        this.ambiguous = [];
        
        this.demo = false;
        
        this.points = 0;
        this.maxPoints = 0;
        
        Util.blinkBoldId = null;
        Util.blinkBoldElement = null;
        
        Demo.pointer.hide();
    },
    
    resetHtmlMoves: function() {
        if (this.demo) {
            this.movesHtml.a = document.getElementById('demo-counter-a');
            this.movesHtml.b = document.getElementById('demo-counter-b');
        } else {
            this.movesHtml.a = document.getElementById('counter-a');
            this.movesHtml.b = document.getElementById('counter-b');
        }
        this.movesHtml.a.innerHTML = 0;
        this.movesHtml.b.innerHTML = 0;
    },
    
    setPoints: function() {
        $(this.pointsHtml).text(this.level * 3);
        $('.game-box').find('.max-points').text(this.level * 3);
        $('.game-box').find('.info').text('Game - level ' + this.level);
    },
    
    setObstacles: function() {
        Util.setGroupColor(this.obstacles, Util.colorObstacle);
    },
    
    bindEvents: function(events) {
        $('.field').on(events, this.fieldClickHandler);
    },
    
    unbindEvents: function(events) {
        $('.field').off(events, this.fieldClickHandler);
    },
    
    fieldsSelected: function(player) {
        if (this.active != player) {
            return false;
        }
        if (this.selectedFields.length == 5) {
            if (player == 'a') {
                Util.hideSelectButton();
            }
            this.selectedFields.sort(Util.sortNumber);
            this.players[this.active] = this.selectedFields;
            this.selectedFields = [];
            this.selected[this.active] = true;
            if (!this.demo) {
                if (player == 'b') {
                    this.startTimer(this.MOVE_TIME);
                } else {
                    Util.stopBlinkBold();
                    this.stopTimer();
                    if (this.selected.b) {
                        this.movesHtml.a.innerHTML = ++this.moves.a;
                        this.updatePoints();
                    }
                    this.systemMove(1);
                }
            }
            //resetActiveCounter();
            this.suggest();
        } else {
            Util.blinkBold($('.dir.start').find('.blink'), 3);
        }
    },
    
    startTimer: function(interval) {
        this.setActive();
        this.timer = interval;
        this.timerHtml.innerHTML = this.timer;
        this.timerId = setInterval(this.tick, 1000);
        
        if (this.selected.a) {
            Util.showDir('move');
            Util.blinkBold($('.dir.move'), 3);
        } else {
            Util.showDir('start');
        }
    },
    
    stopTimer: function() {
        this.setInactive();
        clearInterval(this.timerId);
        if (Game.passed.b.length < 5) {
            Util.showDir('comp');
        } else {
            Util.hideDirs();
        }
    },
    
    setActive: function() {
        this.active = 'a';
        this.nonActive = 'b';
        if (!this.demo) {
            this.movesHtml.b.style['border-bottom'] = 'none';
            this.movesHtml.a.style['border-bottom'] = '3px solid ' + this.colors.a;
        }
    },
    
    setInactive: function() {
        this.active = 'b';
        this.nonActive = 'a';
        if (!this.demo) {
            this.movesHtml.a.style['border-bottom'] = 'none';
            this.movesHtml.b.style['border-bottom'] = '3px solid ' + this.colors.b;
        }
    },
    
    tick: function() {
        Game.timerHtml.innerHTML = --Game.timer;
        //Game.timer--;
        if (Game.timer < 1) {
            
            if (Game.blinkId) {
                clearTimeout(Game.blinkId);
                Game.blinkId = null;
            }
            Util.stopBlinkBold();
            
            for (var i = 0; i < Game.selectedFields.length; i++) {
                if (!Game.selected[Game.active]) {
                    Util.setFieldColor(Game.selectedFields[i], Util.colorOut);
                } else if (Game.players[Game.nonActive].indexOf(Game.selectedFields[i]) > -1) {
                    Util.setFieldColor(Game.selectedFields[i], Util.colorMixed);
                } else {
                    Util.setFieldColor(Game.selectedFields[i], Game.colors[Game.active]);
                }
            }
            if (Game.selected[Game.active] || Game.selected[Game.nonActive]) {
                Game.movesHtml[Game.active].innerHTML = ++Game.moves[Game.active];
                Game.updatePoints();
            }
            Game.stopTimer();
            Game.selectedFields = [];
            Game.suggest();
            if (Game.moves[Game.active] < Game.minMoves) {
                Game.systemMove(0);
            } else if (Game.passed[Game.nonActive].length < 5 ) {
                //Game.setTime();
                Game.systemMove(0);
                Game.startTimer(Game.MOVE_TIME);
            }
        }
    },
    
    systemMove: function(first) {
        
        var playerState = [].concat(this.players.a, this.passed.a);
        
        var extraCalls = 2;
        var that = this;
        $.ajax({
            url: 'index.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'move',
                player: playerState
            },
            success: function(response) {
                if (response) {
                    if (response.valid) {
                        if (response.move) {
                            for (var i = 0; i < response.move.length; i++) {
                                var field = document.getElementById('' + response.move[i]);
                                setTimeout(that.triggerClick, 600 * (i + 1), [field]);
                            }
                        }
                    } else {
                        if (response.message) {
                            that.quit(response.message);
                        } else {
                            that.quit();
                        }
                    }
                } else {
                    if (extraCalls > 0) {
                        extraCalls--;
                        that.systemMove();
                    } else {
                        that.quit('An error occurred while making new move! Sorry, the game is over.');
                    }
                }
            },
            error: function() {
                if (extraCalls > 0) {
                    extraCalls--;
                    that.systemMove();
                } else {
                    that.quit('An error occurred while making new move! Sorry, the game is over.');
                }
            }
        });
    },
    
    setTime: function() {
        $.post('index.php?action=set_time');
    },
    
    triggerClick: function(field) {
        $(field).trigger('systemClick');
    },
    
    restoreGroupColors: function(group) {
        for (var i = 0; i < group.length; i++) {
            var color;
            if (this.players[this.active].indexOf(group[i]) > -1 && this.players[this.nonActive].indexOf(group[i]) > -1) {
                color = Util.colorMixed;
            } else if (this.players[this.active].indexOf(group[i]) > -1) {
                color = this.colors[this.active];
            } else if (this.players[this.nonActive].indexOf(group[i]) > -1) {
                color = this.colors[this.nonActive];
            } else if (group[i] < 3000 || group[i] > 10900) {
                color = Util.colorOut;
            } else {
                color = Util.colorTable;
            }
            Util.setFieldColor(group[i], color);
        }
    },
    /*
    restoreSelectedFields: function() {
        for (var i = 0; i < this.selectedFields.length; i++) {
            if (this.players[this.nonActive].indexOf(this.selectedFields[i]) > -1) {
                Util.setFieldColor(this.selectedFields[i], Util.colorMixed);
            } else {
                Util.setFieldColor(this.selectedFields[i], this.colors[this.active]);
            }
        }
        this.selectedFields = [];
    },
    */
    suggest: function() {
        
        var len = this.selectedFields.length;
        if (len == 0) {
            if (this.suggestFields.length > 0) {
                this.restoreGroupColors(this.suggestFields);
                
                this.suggestFields = [];
                this.suggestGroups = [];
                this.ambiguous = [];
            }
            return;
        }
        
        this.selectedFields.sort(Util.sortNumber);
        
        var map = [];
        
        switch (len) {
            case 1:
                if (map1[this.selectedFields[0]] !== undefined) {
                    map = map1[this.selectedFields[0]];
                }
                break;
            case 2:
                if (map2[this.selectedFields[0]] !== undefined &&
                    map2[this.selectedFields[0]][this.selectedFields[1]] !== undefined) {
                    map = map2[this.selectedFields[0]][this.selectedFields[1]];
                }
                break;
            case 3:
                if (map3[this.selectedFields[0]] !== undefined &&
                    map3[this.selectedFields[0]][this.selectedFields[1]] !== undefined &&
                    map3[this.selectedFields[0]][this.selectedFields[1]][this.selectedFields[2]] !== undefined) {
                        map = map3[this.selectedFields[0]][this.selectedFields[1]][this.selectedFields[2]];
                }
                break;
            case 4:
                if (map4[this.selectedFields[0]] !== undefined &&
                    map4[this.selectedFields[0]][this.selectedFields[1]] !== undefined &&
                    map4[this.selectedFields[0]][this.selectedFields[1]][this.selectedFields[2]] !== undefined &&
                    map4[this.selectedFields[0]][this.selectedFields[1]][this.selectedFields[2]][this.selectedFields[3]] !== undefined) {
                        map = map4[this.selectedFields[0]][this.selectedFields[1]][this.selectedFields[2]][this.selectedFields[3]];
                }
                break;
            case 5:
                if (map5[this.selectedFields[0]] !== undefined &&
                    map5[this.selectedFields[0]][this.selectedFields[1]] !== undefined &&
                    map5[this.selectedFields[0]][this.selectedFields[1]][this.selectedFields[2]] !== undefined &&
                    map5[this.selectedFields[0]][this.selectedFields[1]][this.selectedFields[2]][this.selectedFields[3]] !== undefined &&
                    map5[this.selectedFields[0]][this.selectedFields[1]][this.selectedFields[2]][this.selectedFields[3]][this.selectedFields[4]] !== undefined) {
                        map = map5[this.selectedFields[0]][this.selectedFields[1]][this.selectedFields[2]][this.selectedFields[3]][this.selectedFields[4]];
                }
                break;
        }
        
        var newSuggestFields = [];
        var newSuggestGroups = [];
        var newAmbiguous = [];
        
        if (map !== undefined && map.length > 0) {
            for (var i = 0; i < map.length; i++) {
                var temp = [];
                for (var j = 0; j < map[i].length; j++) {
                    if (this.selectedFields.indexOf(map[i][j]) > -1 
                            || this.obstacles.indexOf(map[i][j]) > -1 
                            || this.players[this.active].indexOf(map[i][j]) > -1) {
                        temp = [];
                        break;
                    }
                    temp.push(map[i][j]);
                }
                if (temp.length > 0) {
                    newSuggestGroups.push(temp);
                    for (var k = 0; k < temp.length; k++) {
                        if (newSuggestFields.indexOf(temp[k]) === -1) {
                            newSuggestFields.push(temp[k]);
                        } else {
                            newAmbiguous.push(temp[k]);
                        }
                    }
                }
            }
        }
        // select all elements marked as a suggestion
        if (this.suggestFields.length > 0) {
            this.restoreGroupColors(this.suggestFields);
        }
        
        if (newSuggestFields.length > 0) {
            Util.setGroupColor(newSuggestFields, Util.colorSuggest);
        }
        
        this.suggestFields = newSuggestFields;
        this.ambiguous = newAmbiguous;
        this.suggestGroups = newSuggestGroups;
    },
    
    blinkId: null,
    
    blink: function(groups) {
        Util.setGroupColor(groups[0], Util.colorBlink);
        Game.blinkId = setTimeout(
            function() {
                Util.setGroupColor(groups[0], Util.colorSuggest);
                Util.setGroupColor(groups[1], Util.colorBlink);
                Game.blinkId = setTimeout(
                    function() {
                        Util.setGroupColor(groups[1], Util.colorSuggest);
                    },
                    300
                );
            },
            300
        );
    },
    
    fieldClickHandler: function(event) {
        var field = event.target;
        var id = parseInt(field.id);
        var systemClick = event.type === 'systemClick';
        if (Game.active == 'b' && !systemClick && event.type !== 'demoClick') {
            return false;
        }
        //var demoClick = event.type === 'demoClick';
        if (!Game.selected[Game.active]) {
            if ((id < 3000 && Game.active == 'a') || (id > 10900 && Game.active == 'b')) {
                if (Game.selectedFields.indexOf(id) > -1) {
                    Game.selectedFields.splice(Game.selectedFields.indexOf(id), 1);
                    Util.setFieldColor(id, Util.colorOut);
                } else {
                    if (Game.selectedFields.length < 5) {
                        Game.selectedFields.push(id);
                        Util.setFieldColor(id, Game.colors[Game.active]);
                    }
                    if (Game.active == 'b' && Game.selectedFields.length == 5) {
                        Game.fieldsSelected('b');
                    }
                }
            }
            return false;
        }
        
        if (Game.players[Game.active].indexOf(id) > -1) {
            if (Game.selectedFields.indexOf(id) > -1) {
                Game.selectedFields.splice(Game.selectedFields.indexOf(id), 1);
                if (Game.players[Game.nonActive].indexOf(id) > -1) {
                    Util.setFieldColor(id, Util.colorMixed);
                } else {
                    Util.setFieldColor(id, Game.colors[Game.active]);
                }
            } else {
                Game.selectedFields.push(id);
                Util.setFieldColor(id, Game.activeColors[Game.active]);
            }
            Game.suggest();
        } else {
            
            if (Game.selectedFields.length && Game.suggestFields.indexOf(id) > -1) {
                
                if (Game.ambiguous.indexOf(id) > -1) {
                    var ambiguousGroups = [];
                    for (var i = 0; i < Game.suggestGroups.length; i++) {
                        if (Game.suggestGroups[i].indexOf(id) > -1) {
                            ambiguousGroups.push(Game.suggestGroups[i]);
                        }
                    }
                    Game.blink(ambiguousGroups);
                    return false;
                }
                
                var to = false;
                for (var i = 0; i < Game.suggestGroups.length; i++) {
                    if (Game.suggestGroups[i].indexOf(id) > -1) {
                        to = Game.suggestGroups[i];
                    }
                }
                
                if (to) {
                    
                    if (Game.blinkId) {
                        clearTimeout(Game.blinkId);
                        Game.blinkId = null;
                    }
                    Util.stopBlinkBold();
                    
                    for (var i = 0; i < Game.selectedFields.length; i++) {
                        var index = Game.players[Game.active].indexOf(parseInt(Game.selectedFields[i]));
                        
                        if (index > -1) {
                            Game.players[Game.active].splice(index, 1);
                            if (Game.players[Game.nonActive].indexOf(parseInt(Game.selectedFields[i])) > -1) {
                                Util.setFieldColor(Game.selectedFields[i], Game.colors[Game.nonActive]);
                            } else if (Game.selectedFields[i] < 3000 || Game.selectedFields[i] > 10900) {
                                Util.setFieldColor(Game.selectedFields[i], Util.colorOut);
                            } else {
                                Util.setFieldColor(Game.selectedFields[i], Util.colorTable);
                            }
                        }
                    }
                    
                    var passedFields = [];
                    var nonActive = Game.nonActive;
                    
                    for (var i = 0; i < to.length; i++) {
                        
                        if ((to[i] < 3000 && Game.active === 'b') || (to[i] > 10900 && Game.active === 'a')) {
                            
                            passedFields.push(to[i]);
                            if (Game.players[Game.nonActive].indexOf(parseInt(to[i]))) {
                                Util.setFieldColor(to[i], Util.colorMixed);
                            } else {
                                Util.setFieldColor(to[i], Game.colors[Game.active]);
                            }
                        } else if (Game.players[Game.nonActive].indexOf(parseInt(to[i])) > -1) {
                            Game.players[Game.active].push(to[i]);
                            Util.setFieldColor(to[i], Util.colorMixed);
                        } else {
                            Game.players[Game.active].push(to[i]);
                            Util.setFieldColor(to[i], Game.colors[Game.active]);
                        }
                        
                    }
                    
                    Game.passed[Game.active] = Game.passed[Game.active].concat(passedFields);
                    
                    Game.movesHtml[Game.active].innerHTML = ++Game.moves[Game.active];
                    
                    if (!Game.demo) {
                        if (systemClick) {
                            if (Game.passed.a.length < 5 ) {
                                Game.setTime();
                                Game.startTimer(Game.MOVE_TIME);
                            } else {
                                Game.saveGame(0);
                            }
                        } else {
                            Game.stopTimer();
                            Game.updatePoints();
                            if (Game.passed.b.length < 5) {
                                Game.systemMove(0);
                            } else {
                                if (Game.passed.a.length < 5) {
                                    Game.systemMove(0);
                                    Game.startTimer(Game.MOVE_TIME);
                                } else {
                                    Game.saveGame(1);
                                }
                            }
                        }
                    }
                    
                    if (passedFields.length > 0) {
                        setTimeout(
                            function() {
                                for (var i = 0; i < passedFields.length; i++) {
                                    if (Game.players[nonActive].indexOf(parseInt(passedFields[i])) > -1) {
                                        Util.setFieldColor(passedFields[i], Game.colors[nonActive]);
                                    } else {
                                        Util.setFieldColor(passedFields[i], Util.colorOut);
                                    }
                                }
                            },
                            500
                        );
                    }
                }
                
                Game.selectedFields = [];
                Game.suggest();
            }
        }
    },
    
    saveGame: function(validate) {
        
        this.unbindEvents('click systemClick');
        
        var playerState = [].concat(this.players.a, this.passed.a);
        var that = this;
        
        $.ajax({
            url: 'index.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'save_game',
                validate: validate,
                player: playerState
            },
            success: function(response) {
                if (response) {
                    if (response.valid) {
                        if (!response.error) {
                            that.highlightPoints(response.points);
                            Util.hideQuitButton();
                            Util.showPlayDemoLink();
                        }
                        if (response.saved) {
                            Standings.refresh();
                            Played.refresh();
                        }
                        if (response.message) {
                            Util.showDir('end', response.message);
                            Util.blinkBold($('.dir.end'), 3);
                        }
                    } else {
                        if (response.message) {
                            that.quit(response.message);
                        } else {
                            that.quit();
                        }
                    }
                    
                }
            }
        });
    },
    
    startDemo: function(obstacles) {
        Util.clearTable();
        this.resetVars();
        this.demo = true;
        
        this.resetHtmlMoves();
        
        this.obstacles = obstacles;
        this.setObstacles();
        Util.showBox(false, 'demo');
        this.unbindEvents('demoClick');
        this.bindEvents('demoClick');
    },
    
    updatePoints: function() {
        if (this.moves.a > this.minMoves) {
            if (this.level > 8) {
                var points = Math.round(3 * this.level - (this.moves.a - this.minMoves) * (this.level / 2 - 2.5));
            } else {
                var points = Math.round(3 * this.level - (this.moves.a - this.minMoves) * this.step);
            }
            //var points = Math.round(3 * this.level - (this.moves.a - this.level) * (this.level / 2 - 2.5));
            if (points < 0) {
                points = 0;
            }
            $(this.pointsHtml).text(points);
        }
    },
    
    highlightPoints: function(points) {
        $(this.pointsHtml).text(points);
        /*
        var i = 0;
        var id;
        id = setInterval(function() {
            $('#points').hide();
            setTimeout(function() {
                $('#points').show();
                i++;
                if (i > 3) {
                    clearInterval(id);
                }
            }, 100);
        }, 110);
        */
        this.pointsHtml.style.color = this.colors.a;
        this.pointsHtml.style['border-bottom'] = '3px solid ' + this.colors.a;
    }
    
};