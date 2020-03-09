<script type="text/javascript">
    function getVar(gurl) {
        var url_string = window.location.href;
        var nurl = new URL(url_string);
        var vurl = nurl.searchParams.get(gurl);
        return vurl;
    }
    localStorage.setItem('room', getVar('room'));
    window.location.href = "index.php";
</script>