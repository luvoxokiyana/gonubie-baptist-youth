<?php
class Database {
    private $host = 'sql100.infinityfree.com';
    private $db_name = 'if0_41572398_church_youth';  // Added if0_ prefix
    private $username = 'if0_41572398';
    private $password = 'GuyGam3r20';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }

    public function setupTables() {
    $conn = $this->getConnection();
    if (!$conn) return false;

    $sql = "
        CREATE TABLE IF NOT EXISTS votes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            poll_type VARCHAR(20) NOT NULL,
            option_id VARCHAR(10) NOT NULL,
            user_ip VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS poll_options (
            id INT AUTO_INCREMENT PRIMARY KEY,
            poll_type VARCHAR(20) NOT NULL,
            option_id VARCHAR(10) NOT NULL,
            option_name VARCHAR(100) NOT NULL,
            display_order INT DEFAULT 0
        );

        CREATE TABLE IF NOT EXISTS suggestions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            suggestion_text TEXT NOT NULL,
            user_ip VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS user_vote_status (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_ip VARCHAR(255) NOT NULL,
            poll_type VARCHAR(20) NOT NULL,
            voted_option_id VARCHAR(10) NOT NULL,
            voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ";
    
    // Drop the old UNIQUE constraint if it exists
    try {
        $conn->exec("ALTER TABLE user_vote_status DROP INDEX unique_user_poll");
    } catch(PDOException $e) {
        // Index might not exist, that's fine
    }
    
    // Add a new UNIQUE constraint that includes device fingerprint
    try {
        $conn->exec("ALTER TABLE user_vote_status ADD UNIQUE KEY unique_user_poll (user_ip, poll_type)");
    } catch(PDOException $e) {
        // Already exists or error - ignore
    }

    try {
        $conn->exec($sql);
        $this->insertDefaultOptions($conn);
        return true;
    } catch(PDOException $e) {
        error_log("Setup error: " . $e->getMessage());
        return false;
    }

        try {
            $conn->exec($sql);
            $this->insertDefaultOptions($conn);
            return true;
        } catch(PDOException $e) {
            error_log("Setup error: " . $e->getMessage());
            return false;
        }
    }

    private function insertDefaultOptions($conn) {
        $stmt = $conn->query("SELECT COUNT(*) FROM poll_options");
        if ($stmt->fetchColumn() > 0) return;

        $defaults = [
            'bible' => [['b1','Jubilee Youth Talk',1],['b2','Jubilee Hot-Takes',2],['b3','Investigating the paranormal',3],['b4',"Temptation- the Why's and How's )",4]],
            'game' => [['g1','Speak and Switch',1],['g2','Minute to Win It',2],['g3','Netball Tournament',3],['g4','Dodgeball',4]],
            'event' => [['e1','Movies Night(The Choosen)',1],['e2','Hemigways(Bowling and Arcade)',2],['e3','Beach Day',3],['e4','Sports Tournament',4]]
        ];

        $insert = $conn->prepare("INSERT INTO poll_options (poll_type, option_id, option_name, display_order) VALUES (?, ?, ?, ?)");
        foreach ($defaults as $pollType => $options) {
            foreach ($options as $opt) {
                $insert->execute([$pollType, $opt[0], $opt[1], $opt[2]]);
            }
        }
    }
}
?>