<?php



$pdo = new PDO('mysql:host=localhost;dbname=deepgame_forum', "deepgame_forum", "pywpMNmSKCUs");


$query = $pdo->prepare(<<<SQL
    SELECT * from post_old                       
SQL
);


$query->execute();

while( $post = $query->fetch(PDO::FETCH_ASSOC) )  {
    
    if($post['parent_id'] !== null)
        continue;
    
    
    
    translatePost($post, "");
};

function translatePost($post, $basethread) {
    global $pdo;
    $post['thread'] = $basethread . $post['id'] . "/";
    
    $query = $pdo->prepare(<<<SQL
    INSERT INTO post_new (id, thread, author, subject, post, timestamp, ip, token)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
SQL
    );
    

    
    $result = $query->execute( array(
        $post['id'],    
        $post['thread'],    
        $post['author'],    
        $post['subject'],    
        $post['post'],    
        $post['timestamp'],    
        $post['ip'],    
        $post['token']    
        )
    );
    
    
    echo "Inserted post<br>";
    
    $query = $pdo->prepare(<<<SQL
    SELECT * from post_old where parent_id = ?
SQL
    );
    
    $query->execute(array($post['id']));
    
    $children = $query->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($children) . " children<br>";
    
    foreach($children as $child) {
        translatePost($child, $post['thread']);
    }
    
}

