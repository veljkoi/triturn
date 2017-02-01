var Ratings = {
    
    limit: 100,
    currentStanding: 0,
    currentRating: null,
    
    refresh: function() {
        var players = this.get();
        this.clear();
        if (players.length > 0) {
            this.show(players);
        }
    },
    get: function() {
        var players = [];
        
        $.ajax({
            url: 'index.php',
            async: false,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_ratings',
                limit: this.limit
            },
            success: function(response) {
                if (response) {
                    players = response;
                }
            }
        });
        return players;
    },
    show: function(players) {
        //for (var j = 0; j < 100; j++) {
        for (var i = 0; i < players.length; i++) {
            if (this.currentRating != players[i].rating) {
                this.currentStanding++;
                this.currentRating = players[i].rating;
            }
            
            var player = '<div class="player" data-id="' + players[i].id + '" >';
            player += '<input type="hidden" class="id" value="' + players[i].id + '" />';
            player += '<div class="standing">' + this.currentStanding + '.</div>';
            player += '<div class="name">' + players[i].username + '</div>';
            player += '<div class="rating">' + players[i].rating + '</div>';
            player += '</div>';
            $(player).appendTo('.players');
        }
        //}
    },
    clear: function() {
        $('.players').html('');
        this.currentStanding = 0;
        this.currentRating = null;
    }
};