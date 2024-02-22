<?php

class ModelExtensionModuleMembership extends Model {
    public function addNewMembership( $data ) {
        $this->db->query( 'INSERT INTO ' . DB_PREFIX . "membership (name, amount, discount) VALUES ('" . trim($this->db->escape( $data[ 'name' ] )) . "', '" . ( float )trim($data[ 'amount' ] ). "', '" . ( float )trim($data[ 'discount' ] ). "')" );
    }

    public function editMembership( $data ) {
        $this->db->query( 'UPDATE ' . DB_PREFIX . "membership SET name = '" . trim($this->db->escape( $data[ 'name' ] )) . "', amount = '" . ( float )$data[ 'amount' ] . "', discount = '" . ( float )$data[ 'discount' ] . "', updated_at = NOW() WHERE id = '" . ( int )$data['membership_id'] . "'" );
    }

    public function deleteMembership( $membership_id ) {
        $this->db->query( 'DELETE FROM ' . DB_PREFIX . "membership WHERE id = '" . ( int )$membership_id . "'" );
    }

    public function getMembership( $membership_id ) {
        $query = $this->db->query( 'SELECT * FROM ' . DB_PREFIX . "membership WHERE id = '" . ( int )$membership_id . "'");
        return $query->row;
    }

    public function getMemberships($order = false) {
        $sql =  'SELECT * FROM ' . DB_PREFIX . 'membership';
        if($order)
            $sql .= ' ORDER BY amount ASC';
        
        $query = $this->db->query($sql);
        
        return $query->rows;
    }

    public function addMessage( $data ) {
        $this->db->query( 'INSERT INTO ' . DB_PREFIX . "firebase_messages (message, sent_at) VALUES ('" . $data . "', NOW())" );
    }

    public function getMessages() {
        $query = $this->db->query( 'SELECT * FROM ' . DB_PREFIX . 'firebase_messages' );
        return $query->rows;
    } 

    public function addConfig( $data ) {
        $this->db->query( 'UPDATE ' . DB_PREFIX . "firebase_config SET api_key = '" . $this->db->escape( $data[ 'api_key' ] ) . "', message_keep = '" . $this->db->escape( $data[ 'message_keep' ] ) . "', message_promoted = '" . $this->db->escape( $data[ 'message_promoted' ] ) . "', message_demoted = '" . $this->db->escape( $data[ 'message_demoted' ] ) . "', expiration_duration = '" . $this->db->escape( $data['expiration_duration'] ) . "', minimum_points = '" . $this->db->escape( $data['minimum_points'] ) . "', expiration_criteria = '" . $this->db->escape( $data['expiration_criteria'] ) . "' WHERE id = 1" );
    }

    public function getConfig() {
        $query = $this->db->query( 'SELECT * FROM ' . DB_PREFIX . 'firebase_config WHERE id = 1'  );
        return $query->row;
    }

    public function editConfig($data) {
        $query = $this->db->query( "UPDATE " . DB_PREFIX . "firebase_config SET api_key = '" . $this->db->escape( $data[ 'name' ] ) . "', message = '" . $this->db->escape( $data[ 'name' ] ) . "', WHERE id = 1"  );
        return $query->row;
    }

    public function sumRewardPointCustomer($customer_ID, $duration = null) {
        $sql = "SELECT SUM(points) as points FROM " . DB_PREFIX . "customer_reward WHERE customer_id = " . $customer_ID;
        if(isset($duration) && $duration != null){
            $sql.= " AND date_added	 <= DATEADD(M, $duration, GETDATE())"; 
        }
        $query = $this->db->query( $sql );
        return $query->row['points'];
    }

    public function getSentNotificaitonCount() {
        $sql = "SELECT count(*) as count FROM " . DB_PREFIX . "firebase_messages";
        return $this->db->query( $sql )->row['count'];
    }

    public function getCustomerMemberships($customer_id) {
        $query = $this->db->query("SELECT
            (SELECT name FROM och6_membership mm WHERE mm.id = ext.id ORDER BY mm.id ASC LIMIT 1) AS current_mm,
            (SELECT amount FROM och6_membership mm WHERE mm.id = ext.id ORDER BY mm.id ASC LIMIT 2) AS next_mm_pts, 
        total_reward_points, updated_at FROM ext_customer_membership ext WHERE customer_id =" . $customer_id );
        
        return $query->row;
    }

    public function updateCustomerMembership($customer_id, $membership_id, $newly_points) {
        if($this->isFirstTimeReward($customer_id)) {
            $this->db->query("UPDATE ext_customer_membership SET membership_id = '" . $this->db->escape( $membership_id ) . "',  total_reward_points = total_reward_points + '" . $this->db->escape( $newly_points ) . "', updated_at = NOW() WHERE customer_id = ".$customer_id);
        }else{
            $this->db->query("INSERT INTO ext_customer_membership (customer_id, membership_id, total_reward_points, created_at, updated_at) VALUES ('" . $customer_id . "', '" . $membership_id . "', '" . $newly_points . "', NOW(), NOW() )");
        }
        return $this->db->getLastId();
    }

    public function deleteCustomerMembership($customer_id, $newly_points) {
        $this->db->query("UPDATE ext_customer_membership SET total_reward_points = total_reward_points - '" . $this->db->escape( $newly_points ) . "', updated_at = NOW() WHERE customer_id = ".$customer_id);
        return $this->db->getLastId();
    }

    public function getLastReward($customer_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_reward WHERE customer_id = " . $customer_id . " ORDER BY date_added DESC LIMIT 1");
        return $query->row['date_added'];
    }

    public function isFirstTimeReward($customer_id) {
        $query = $this->db->query("SELECT COUNT(*) as count FROM ext_customer_membership WHERE customer_id = ". $customer_id);
        return $query->row['count'] > 0;
    }
}
