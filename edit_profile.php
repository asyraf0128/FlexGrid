<?php
    require_once 'header.php';

    if (!$loggedin) die("</div></body></html>");

    echo "<div class='edit_profile_container'>";
    echo "<h3>Your Profile</h3>";

    $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");

    if (isset($_POST['text']))
    {
        $text = sanitizeString($_POST['text']);
        $text = preg_replace('/\s\s+/', '', $text);
        $workouts = sanitizeString($_POST['workouts']);
        $height = sanitizeString($_POST['height']);
        $weight = sanitizeString($_POST['weight']);
        $country = sanitizeString($_POST['country']);
        $image = NULL;

       // Check if an image was uploaded
    if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'] != '') {
        $image = file_get_contents($_FILES['image']['tmp_name']);
        $image = $connection->real_escape_string($image);
    }

    if ($result->num_rows) {
        // Update the existing profile
        if ($image) {
            queryMysql("UPDATE profiles SET text='$text', workouts='$workouts', height='$height', weight='$weight', country='$country', image='$image' WHERE user='$user'");
        } else {
            queryMysql("UPDATE profiles SET text='$text', workouts='$workouts', height='$height', weight='$weight', country='$country' WHERE user='$user'");
        }
    } else {
        // Insert a new profile
        queryMysql("INSERT INTO profiles (user, text, workouts, height, weight, country, image) VALUES ('$user', '$text', '$workouts', '$height', '$weight', '$country', '$image')");
    }

    header("Location: my_profile.php");
    exit;
}
    else
    {
        if ($result->num_rows)
        {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $text = stripslashes($row['text']);
            $workouts = $row['workouts'];
            $height = $row['height'];
            $weight = $row['weight'];
            $country = $row['country'];
            $image = $row['image'];

        }
        else 
        {
            $text = "";
            $workouts = 0;  
            $height = "";
            $weight = "";
            $country = "";
            $image = NULL;
        }
    }

    $text = stripslashes(preg_replace('/\s\s+/', '', $text));

showProfile($user);

echo <<<_END
<div class ='edit_profile_container'>
        <form data-ajax='false' method='post'
            action='edit_profile.php' enctype='multipart/form-data'>
        <h3>Enter or edit your details and/or upload an image</h3>
        <textarea name='text'>$text</textarea><br>
        Workouts: <input type ='number' name='workouts' value='$workouts'><br>
        Height(cm): <input type='text' name='height' value='$height'><br>
        Weight(kg): <input type='text' name='weight' value='$weight'><br>
        Country: 
        <select name='country'>
_END;

$countries = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo, Democratic Republic of the", "Congo, Republic of the", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor (Timor-Leste)", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Ivory Coast", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"];
foreach ($countries as $c) {
    $selected = ($c == $country) ? "selected" : "";
    echo "<option value='$c' $selected>$c</option>";
}

echo <<<_END
        </select><br>
        Profile Image: <input type='file' name='image' size='14'><br>
        <input type='submit' value='Save Profile'>
    </form>
</div><br>
_END;
 ?>
 