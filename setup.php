<!DOCTYPE html>
<html>
    <head>
        <title>Setting up databse</title>
    </head>
    <body>

        <h3>Setting up...</h3>

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
        require_once 'functions.php';

        createTable('members',
                    'user VARCHAR(16),
                    pass VARCHAR(16),
                    INDEX(user(6))');

        createTable('messages',
                    'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    auth VARCHAR(16),
                    recip VARCHAR(16),
                    pm CHAR(1),
                    time INT UNSIGNED,
                    message VARCHAR(4096),
                    INDEX(auth(6)),
                    INDEX(recip(6))');
        
        createTable('friends',
                    'user VARCHAR(16),
                    friend VARCHAR(16),
                    INDEX(user(6)),
                    INDEX(friend(6))');
                    
        createTable('profiles',
                    'user VARCHAR(16),
                    text VARCHAR(4096),
                    INDEX(user(6))');

        createTable('splits',
                    'user VARCHAR(16),
                    split VARCHAR(4096),
                    INDEX(split(6))');

        createTable('posts',
                    'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user VARCHAR(16),
                    title VARCHAR(255) NOT NULL,
                    slug VARCHAR (255) NOT NULL UNIQUE,
                    description TEXT,
                    split VARCHAR(255),
                    media TEXT,
                    visibility ENUM(\'public\', \'private\') DEFAULT \'public\',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    is_workout BOOLEAN DEFAULT FALSE,
                    num_replies INT UNSIGNED DEFAULT 0,
                    num_views INT UNSIGNED DEFAULT 0');
        
        createTable('comments',
                    'id INT AUTO_INCREMENT PRIMARY KEY,
                    user VARCHAR(16),
                    post_id INT,
                    comment TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
                
        
        ?>
            <br>...done.
    </body>
</html>