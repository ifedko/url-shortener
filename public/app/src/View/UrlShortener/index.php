<?php include $pathToLayout . 'header.php'; ?>

<form action="javascript:alert('TODO');">
    <table>
        <tr>
            <th>Long URL</th>
            <th>Short URL</th>
        </tr>
        <tr>
            <td>
                <input type="url" name="url">
                <input type="submit" value="Do!">
            </td>
            <td id=result></td>
        </tr>
    </table>
</form>
<script type="text/javascript" src="/public/js/url_shortener.js"></script>
<script type="text/javascript">
    window.onload = function () {
        ifedkoUrlShortener.setup({
            requestUri: "http://url-shortener.local:8081/short_url"
        });
    };
</script>

<?php include $pathToLayout . 'footer.php'; ?>
