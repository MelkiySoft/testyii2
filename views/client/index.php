<div style="position:  relative; width:  500px;">
    <div class="forms">
        <div class="form">
            <input type="text" name="value" size="25" title="Value" value="">
            <input type="button" onclick="add(this.parentElement)" value="add">
        </div>
        <div class="form">
            <input type="text" name="value" size="25" title="Value" value="">
            <input type="button" onclick="remove(this.parentElement)" value="remove">
        </div>
    </div>
    <div class="forms" style="position:  relative; right:  -330px; top: -50px;">
        <div class="form">
            <input type="button" onclick="feed()" value="feed" style="height:  50px; width:  100px;">
        </div>
    </div>
</div>
<script>
    var id = '';
    var user = '';
    var sha1 = '';

    function ajaxGet(url, callback) {
        var f = callback || function(data) {};
        var request = new XMLHttpRequest();

        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                f(request.responseText);
            }
        }

        request.open('GET', url);
        request.send();
    }

    function getsha1(str) {

        ajaxGet('sha1.php?str=' + str, function(data){
            sha1 = data;
        });

    }

    function gotourlAdd(id, user, secret) {
        location.href = ("http://test1.yii2.loc/api/add?id=" + id + "&user=" + user + "&secret=" + sha1);

    }
    function gotourlRemove(id, user, secret) {
        location.href = ("http://test1.yii2.loc/api/remove?id=" + id + "&user=" + user + "&secret=" + sha1);

    }
    function gotourlFeed(id, secret) {
        location.href = ("http://test1.yii2.loc/api/feed?id=" + id + "&secret=" + sha1);

    }
    function add(rrr) {
        user = rrr.children[0].value;
        id = 'ran' + Math.random().toString(36).substr(2,8) + Date.now().toString(36) + new Date().getTime();
        var str = id + user;
        getsha1(str);
        setTimeout("gotourlAdd(id, user, sha1)", 500);
    }

    function remove(rrr) {
        user = rrr.children[0].value;
        id = 'ran' + Math.random().toString(36).substr(2,8) + Date.now().toString(36) + new Date().getTime();
        var str = id + user;
        getsha1(str);
        setTimeout("gotourlRemove(id, user, sha1)", 500);
    }

    function feed() {
        id = 'ran' + Math.random().toString(36).substr(2,8) + Date.now().toString(36) + new Date().getTime();
        getsha1(id);
        setTimeout("gotourlFeed(id, sha1)", 500);
    }
</script>