var Demo = {
    
    level: 0,
    
    moves: {
        a: [],
        b: []
    },
            
    rawMoves: {
        a: [],
        b: []
    },
            
    cursor: {
        a: -1,
        b: -1
    },
            
    cursorLimit: {
        a: 0,
        b: 0
    },
            
    blockClick: 0,
    
    played: false,
    
    pointer: null,
    
    timeout: 400,
    
    firstMessage: "Click on right-arrow and left-arrow buttons to go through the demo. On the table, you can watch a sample game and explanations are shown here.",
    messages: [],    
    noteClass: [],
    
    resetParams: function() {
        this.level = 0;
        this.moves = {
            a: [],
            b: []
        };
        this.rawMoves = {
            a: [],
            b: []
        };
        this.cursor = {
            a: -1,
            b: -1
        };
        this.cursorLimit = {
            a: 0,
            b: 0
        };
        this.blockClick = 0;
        this.played = false;
        this.timeout = 400;
        Util.showSelectButton();
        
        this.messages = [
            "<strong>Selection</strong>: Pick 5 triangles from the white section on the left. If you click on a triangle inside this section it turns red and is picked. If you click on it again it turns white and is not picked. Click on SELECT button to select them. You have 60 seconds for this.",
            "<strong>Goal</strong>: The goal of the game is to move all 5 triangles to the white section on the right. You must avoid black obstacles.",
            "<strong>Moving</strong>: When you click on a red triangle it turns brighter and is selected for moving. If you click on it again it returns to its basic color and can't be moved.",
            "You can move 1, 2, 3, 4 or 5 triangles at once. If you want to move only one triangle, click on it - it is selected for moving now. It is moved by turning it over one of its edges. Triangles to where it is allowed to be moved become yellow and suggest you all the possibilities. If you click on a yellow suggestion your selected triangle is moved.",
            "If you want to move: 2, 3, 4 or 5 triangles at once, they need to be connected by their edges, i.e. they form a geometric shape. That geometric shape is moved by turning it over one of its edges. Yellow suggestions show you all the possibilities. You decide where you want to move it to and click on a suggestion. All 2, 3, 4 or 5 selected triangles are moved now using only 1 move.",
            "This is the purpose of the game, to use as smallest number of moves as possible to move triangles to the white section on the right.<br>You have 30 seconds to make a move.",
            //"<p>If you click on a yellow suggestion and it becomes to blink, then you picked a suggestion that is ambiguous. The blinking shows you which suggestions you should click on.</p>",
            "<strong>Computer</strong>: When you start a game, you play versus computer which starts from the right section of the table and goes in the opposite direction, to the left white section. Computer's triangles are blue.",
            "<strong>Points</strong>: By playing games of higher level and by making less moves, you can score more points.",
            "<strong>Registered</strong>: For registered players (only username and password required), their played games are remembered and last 20 played games are shown in the History table. From the table History, a player can replay a played game to see all her/his moves as well as computer moves. Also, their rating is shown in the Standings table. The rating is calculated by averaging points of last 20 played games."
        ];
        this.noteClass = ['left', 'right', '', '', '', '', 'right blue', '', ''];
    },
    
    startPlayed: function(id) {
        Util.hideMessage('table');
        this.resetParams();
        this.played = true;
        this.timeout = 300;
        Util.hideSelectButton();
        
        var played = Played.get(id);
        this.level = played.level;
        this.moves.a = played.a_moves;
        this.moves.b = played.b_moves;
        this.rawMoves.a = played.a_raw_moves;
        this.rawMoves.b = played.b_raw_moves;
        this.cursorLimit.a = played.a_moves.length;
        this.cursorLimit.b = played.b_moves.length;
        this.blockClick = 0;
        
        $('.demo-box').find('.info').html(played.start_time + '&nbsp;&nbsp;&nbsp; level ' + played.level + '&nbsp;&nbsp;&nbsp; points ' + played.points + '/' + played.max_points);
        Game.startDemo(played.obstacles);
        this.showB();
        Util.scroll('table');
    },
    
    startDemo: function() {
        this.resetParams();
        
        var demo = this.getDemo();
        this.level = demo.level;
        $('.demo-box').find('.info').text('Demo - level ' + demo.level);
        this.moves.a = demo.moves;
        this.rawMoves.a = demo.raw_moves;
        this.cursorLimit.a = demo.moves.length;
        this.blockClick = 0;
        Game.startDemo(demo.obstacles);
        this.pointer.show();
        this.hideB();
        
        var points = this.level * 3;
        this.messages[this.cursorLimit.a - 1] = "GAME OVER - SCORE: " + points + "/" + points;
        this.noteClass[this.cursorLimit.a - 1] = 'right';
        Util.hideDirsBox();
        Util.showDemoNotes();
        //this.showMessage(this.firstMessage);
        this.showFirstMessage();
    },
    
    hideB: function() {
        $('.demo-box').find('.b-box').hide();
    },
    
    showB: function() {
        $('.demo-box').find('.b-box').show();
    },
    
    getDemo: function() {
        var demo;
        $.ajax({
            url: 'index.php',
            async: false,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_demo'
            },
            success: function(response) {
                if (response) {
                    demo = response;
                }
            }
        });
        return demo;
    },
    
    move: function(player, direction) {
        if (this.blockClick) {
            return false;
        } else {
            this.blockClick = 1;
        }
        
        this.showMessage(direction);
        
        if (direction) {
            this.cursor[player]++;
            if (this.cursor[player] < this.cursorLimit[player]) {
                if (player == 'a') {
                    Game.setActive();
                } else {
                    Game.setInactive();
                }
                var nextMove = this.moves[player][this.cursor[player]];
                if (nextMove.length > 0) {
                    this.blockClick = nextMove.length;
                    if (player == 'a' && Game.selected.a == false) {
                        this.blockClick++;
                    }
                    for (var i = 0; i < nextMove.length; i++) {
                        var field = document.getElementById('' + nextMove[i]);
                        setTimeout(Demo.triggerClick, Demo.timeout * (i + 1), [field]);
                    }
                    if (player == 'a' && Game.selected.a == false) {
                        setTimeout(Demo.triggerSelectClick, Demo.timeout * (i + 1));
                        if (this.cursor[player] > 0) {
                            Game.movesHtml[Game.active].innerHTML = ++Game.moves[Game.active];
                        }
                    }
                } else {
                    if (this.cursor[player] > 0) {
                        Game.movesHtml[Game.active].innerHTML = ++Game.moves[Game.active];
                    }
                    this.blockClick = 0;
                }
            } else {
                this.cursor[player]--;
                this.blockClick = 0;
            }
        } else {
            this.cursor[player]--;
            if (this.cursor[player] > -2) {
                if (player == 'a') {
                    Game.setActive();
                } else {
                    Game.setInactive();
                }
                for (var i = 0; i < Game.players[Game.active].length; i++) {
                    //Game.players[Game.active].splice(index, 1);
                    if (Game.players[Game.nonActive].indexOf(parseInt(Game.players[Game.active][i])) > -1) {
                        Util.setFieldColor(Game.players[Game.active][i], Game.colors[Game.nonActive]);
                    } else if (Game.players[Game.active][i] < 3000 || Game.players[Game.active][i] > 10900) {
                        Util.setFieldColor(Game.players[Game.active][i], Util.colorOut);
                    } else {
                        Util.setFieldColor(Game.players[Game.active][i], Util.colorTable);
                    }
                }
                Game.players[player] = [];
                if (this.cursor[player] > -1) {
                    var backMove = this.rawMoves[player][this.cursor[player]];
                    
                    if (backMove.length < 1) {
                        if (!this.played) {
                            Util.showSelectButton();
                        }
                        Game.selected[Game.active] = false;
                    } else {
                        for (var i = 0; i < backMove.length; i++) {
                            if ((backMove[i] < 3000 && player == 'b') || (backMove[i] > 10900 && player == 'a')) {
                                continue;
                            }
                            if (Game.players[Game.nonActive].indexOf(parseInt(backMove[i])) > -1) {
                                Util.setFieldColor(backMove[i], Util.colorMixed);
                            } else {
                                Util.setFieldColor(backMove[i], Game.colors[Game.active]);
                            }
                            Game.players[player].push(parseInt(backMove[i]));
                        }
                    }
                    //Game.players[player] = backMove;
                    if (Game.moves[Game.active] > 0) {
                        Game.moves[Game.active]--;
                    }
                } else {
                    if (player == 'a' && !this.played) {
                        Util.showSelectButton();
                        //this.showMessage(this.firstMessage);
                        this.showFirstMessage();
                    }
                    Game.selected[Game.active] = false;
                }
                Game.movesHtml[Game.active].innerHTML = Game.moves[Game.active];
            } else {
                this.cursor[player]++;
            }
            this.blockClick = 0;
        }
    },
        
    triggerClick: function(field) {
        if (Demo.played) {
            $(field).trigger('demoClick');
            Demo.blockClick--;
        } else {
            var offset = $(field).offset();
            var id = $(field).attr('id');
            var dir = id.charAt(id.length - 2);
            if (dir == '1') {
                offset.top += 15;
                offset.left += 30;
            } else {
                offset.top += 30;
                offset.left += 15;
            }
            Demo.pointer.offset({top: offset.top, left: offset.left});

            setTimeout(function() {
                $(field).trigger('demoClick');
                Demo.blockClick--;
            }, 200);
        }
    },
    
    triggerSelectClick: function() {
        var $selectButton = $('.demo-box').find('.select-button');
        
        if (!Demo.played) {
            var offset = $selectButton.offset();
            offset.top += 15;
            offset.left += 54;
            Demo.pointer.offset({top: offset.top, left: offset.left});
        }
        
        $selectButton.css({"color": "#333", "border-color": "#888", "outline": "0"});
        setTimeout(function() {
            $selectButton.css({"color": "#555", "border-color": "#BBB", "outline": "initial"});
            Game.fieldsSelected('a');
            Game.selected.a = true;
            Demo.blockClick = 0;
        }, 500);
    },
    
    showMessage: function(direction) {
        if (!this.played) {
            if (direction && this.messages[this.cursor.a + 1]) {
                //Util.showMessage(this.messages[this.cursor.a + 1]);
                $('.demo-note').removeClass('left right blue').addClass(this.noteClass[this.cursor.a + 1]).html(this.messages[this.cursor.a + 1]).show();
            } else if (this.cursor.a > -1 && this.cursor.a < (this.cursorLimit.a - 1)) {
                //Util.hideMessage();
                $('.demo-note').hide();
            }
        }
    },
    
    showFirstMessage: function() {
        $('.demo-note').removeClass('left right blue').html(this.firstMessage).show();
    }
    
};