<!DOCTYPE html>
<html>
<head>
    <title>Form</title>
</head>
<body>
    <form action="readForm.php" method="post">
        <label>Name: </label>
        <input type="text" name="name"><br><br>

        <label>Age: </label>
        <input type="text" name="age"><br><br>

        <label>Gender: </label>
        <input type="radio" name="gender" value="Male"> Male
        <input type="radio" name="gender" value="Female"> Female<br><br>

        <label>Title: </label>
        <input type="checkbox" name="title[]" value="Prof"> Prof
        <input type="checkbox" name="title[]" value="Dr"> Dr<br><br>

        <label>Hobby: </label>
        <select name="hobby[]" multiple size="4">
            <option value="reading">reading</option>
            <option value="swimming">swimming</option>
            <option value="basketball">basketball</option>
            <option value="football">football</option>
        </select><br><br>

        <label>Comments: </label><br>
        <textarea name="comments" rows="5" cols="40"></textarea><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
