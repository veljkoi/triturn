var Standings = {
    
    refresh: function() {
        this.clear();
        this.get();
    },
    get: function() {
        $.ajax({
            url: 'index.php?action=get_standings',
            type: 'GET',
            success: function(response) {
                if (response) {
                    $('#standings').find('div').append($(response));
                }
            }
        });
    },
    clear: function() {
        $('#standings').find('div').html('');
    }
};