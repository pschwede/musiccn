<?php include 'globalheader.php'; ?>
    <script type="text/javascript">
    var lastq = '';
    var lastf = '';
    
    function mmss(t) {
        if(t=='undefined')
            t=0;
        min = Math.floor(parseInt(t)/60)
        sec = (parseInt(t)%60);
        return ''+(min<10?'0'+min:min)+":"+(sec<10?'0'+sec:sec);
    };
    
    function force(trackid) {
        $.ajax({
            type:       'GET',
            url:        'ajax.php',
            datatype:   'text',
            data:       'a=force&trackid='+trackid+
                '&dj=<?php echo $_SESSION['id']; ?>',
            success:    function(msg) {
                if(parseInt(msg)>0) {
                    $('#alert').text("Track forced and will likely be played in "+mmss(msg)+" minutes.").slideDown('slow');
                    setTimeout("$('#alert').slideUp('slow')", 3000);
                } else {
                    $('#alert').text("Could't force track by "+msg).slideDown('slow');
                    setTimeout("$('#alert').slideUp('slow')", 3000);
                }
            }
        });
        $("a:contains('(f)')").fadeOut('slow');
    }
    
    function handleJSON(json) {
        var list;
        switch(lastf) {
        case 'titlesofalbum':
            $('#titlesofalbum').hide();
            var album = "";
            for(i=0; i<json.length; i++) {
                if(album != json[i].album) {
                    $('#titlesofalbum').append(
                        '<div class="track stripe" style="height: 100px; background:url('+json[i].cover+') no-repeat">'+
                        '<div class="title">'+
                            json[i].artist+'<br/>'+
                            json[i].album+'<br/>'+
                            '<form method="post" target="_parent" action="staff.php?a=edit">'+
                                '<input type="hidden" name="q" value="'+json[i].album+'">'+
                                '<input type="image" img="templates/img/edit.gif" alt="Edit this album">'+
                            '</form>'+
                        '</div>'
                        );
                    album = json[i].album;
                }
                $('#titlesofalbum').append(
                    '<li><div><div style="width:20%; float:left;">'+mmss(json[i].duration)+'</div>'+
                    '<div style="width: 60%; float:left;">'+json[i].title+'</div><div style="float:left; width:20%;">'+
                    '<a href="javascript:force('+json[i].id+');">(f)</a>'+
                    '</div></div></li>'
                    );
            }
            $('#titlesofalbum').fadeIn('slow');
            break;
        case 'albumsofgenre':
            //$('#albumsofgenre').hide().append('<li><a href="javascript:loadList(\'albumsofgenre\',\'..\');">..</a></li>');
            for(i=0; i<json.length; i++)
                $('#albumsofgenre').append('<li><a href="javascript:loadList(\'titlesofalbum\',\''+json[i].album.split("'").join(".")+'\');">'+
                        '<div class="album" title="'+json[i].artist+' - '+json[i].title+'"><img src="'+json[i].cover+'" alt="'+json[i].artist+' - '+json[i].title+'"></div>'+
                        '</a></li>');
            $('#albumsofgenre').fadeIn('slow');
            break;
        case 'genre':
        default:
            $('#genre').append('<li><a href="javascript:loadList(\'genre\',\'..\');">..</a></li>');
            for(i=0; i<json.length; i++)
                $('#genre').append('<li><a href="javascript:loadList(\'albumsofgenre\',\''+json[i].split("'").join('.')+'\');">'+json[i]+'</a></li>');
        }
    }
    
    function loadList(f, q, t) {
        lastq = q;
        lastf = (t?t:f);
        if(q=='..')
            $('#'+lastf+'searchbar').val('');
        if(q.length > 0) {
            $('#'+lastf).replaceWith('<ul class="list" id="'+lastf+'"></ul>');
            $.getJSON(
                'ajax.php?a=explore&q='+q+'&f='+f,
                function(json) {
                    handleJSON(json);
                });
        }
    }
    
    $("document").ready(function() {
        loadList('genre', '.');
        //loadList('albumsofgenre', '.');
        //loadList('titlesofalbum', '');
        
        $('#generalsearchbar').keyup(function() {
           loadList('genre', $(this).val());
           loadList('artist', $(this).val(),'albumsofgenre');
           loadList('titlesofalbum', $(this).val());
        });
        $('#genresearchbar').keyup(function(event) {
           loadList('genre', $(this).val());
        });
        $('#albumsofgenresearchbar').keyup(function(event) {
           loadList('artist', $(this).val(),'albumsofgenre');
        });
        $('#titlesofalbumsearchbar').keyup(function(event) {
           if($(this).val().length>2)
               loadList('title', $(this).val(),'titlesofalbum');
        });
    });
    </script>
    <body>
        <div id="alert" class="alert">
            <span>
                <?php echo $alert; ?>
            </span>
        </div>
        <div class="head stripe">
            <a href="./staff.php">
                Musiccn
            </a>
        </div>
