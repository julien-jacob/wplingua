<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>wpLingua : Get free API key</title>
</head>
<body>
    
<form action="/v0.0/last/" method="post">
    <fieldset>
        <label for="r">Mail adress:</label>
        <input type="text" name="r" id="r" value="register" hidden>
    </fieldset>
    <fieldset>
        <label for="mail_address">Mail adress:</label>
        <input type="email" name="mail_address" id="mail_address">
    </fieldset>
    <fieldset>
        <label for="website">Website</label>
        <input type="url" name="website" id="website">
    </fieldset>
    <fieldset>
        <label for="language_original">Language source:</label>
        <input type="text" name="language_original" id="language_original" maxlength="2">
    </fieldset>
    <fieldset>
        <label for="languages_target">Language source:</label>
        <input type="text" name="languages_target" id="languages_target" value="all">
    </fieldset>
    <fieldset>
        <input type="submit" value="Get API key">
    </fieldset>
</form>
</body>
</html>