var Played = {
    
    refresh: function() {
        this.clear();
        this.getList();
    },
    getList: function() {
        $.ajax({
            url: 'index.php?action=get_played_list',
            type: 'GET',
            success: function(response) {
                if (response) {
                    $('#history').find('div').append($(response));
                }
            }
        });
    },
    clear: function() {
        $('#history').find('div').html('');
    },
    get: function(id) {
        var played;
        $.ajax({
            url: 'index.php',
            async: false,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_played',
                id: id
            },
            success: function(response) {
                if (response) {
                    played = response;
                }
            }
        });
        return played;
    }
    
};