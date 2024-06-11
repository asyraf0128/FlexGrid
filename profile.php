<?php
    require_once 'header.php';

    if (!$loggedin) die("</div></body></html>");

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

        if ($result->num_rows)
            queryMysql("UPDATE profiles SET text='$text', workouts='$workouts', height='$height', weight='$weight', country='$country' where user='$user'");
        else queryMysql("INSERT INTO profiles (user, text, workouts, height, weight, country)VALUES('$user', '$text', '$workouts', '$height', '$weight', '$country')");      
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

        }
        else 
        {
            $text = "";
            $workouts = 0;  
            $height = "";
            $weight = "";
            $country = "";
        }
    }

    $text = stripslashes(preg_replace('/\s\s+/', '', $text));

    if (isset($_FILES['image']['name']))
    {
        $saveto = "$user.jpg";
        move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
        $typeok = TRUE;

        switch($_FILES['image']['type'])
        {
            case "image/gif": $src = imagecreatefromgif($saveto); break;
            case "image/jpeg": // Both regular and progressive jpegs
            case "image/pjpeg": $src = imagecreatefromjpeg($saveto); break;
            case "image/png": $src = imagecreatefrompng($saveto); break;
            default: $typeok = FALSE; break;  
        }

        if ($typeok)
        {
            list($w, $h) = getimagesize($saveto);

            $max = 100;
            $tw = $w;
            $th = $h;

            if ($w > $h && $max < $w)
            {
                $th = $max / $w * $h;
                $tw = $max;
            }
            elseif ($h > $w && $max < $h)
            {
                $tw = $max / $h * $w;
                $th = $max;
            }
            elseif ($max < $w)
            {
                $tw = $th = $max;
            }

            $tmp = imagecreatetruecolor($tw, $th);
            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
            imageconvolution($tmp, array(array(-1, -1, -1),
                array(-1, 16, -1), array(-1, -1, -1)), 8, 0);
            imagejpeg($tmp, $saveto);
            imagedestroy($tmp);
            imagedestroy($src);
        }
    }


showProfile($user);

echo <<<_END
        <form data-ajax='false' method='post'
            action='profile.php' enctype='multipart/form-data'>
        <h3>Enter or edit your details and/or upload an image</h3>
        <textarea name='text'>$text</textarea><br>
        Workouts: <input type ='number' name='workouts' value='$workouts'><br>
        Height: <input type='text' name='height' value='$height'><br>
        Weight: <input type='text' name='weight' value='$weight'><br>
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
</body>
</html>
_END;
?>
 