<?php

class ModelExtensionModuleMembership extends Model {
    public function addMembership( $data ) {
        //$this->db->query( 'INSERT INTO ' . DB_PREFIX . "membership (name, amount) VALUES ('" . $this->db->escape( $data[ 'name' ] ) . "', '" . ( float )$data[ 'amount' ] . "')" );
    }

    public function editMembership( $data ) {
        $this->db->query( 'UPDATE ' . DB_PREFIX . "membership SET name = '" . $this->db->escape( $data[ 'name' ] ) . "', amount = '" . ( float )$data[ 'amount' ] . "', updated_at = NOW() WHERE id = '" . ( int )$data[ 'membership_id' ] . "'" );
    }

    public function deleteMembership( $membership_id ) {
        $this->db->query( 'DELETE FROM ' . DB_PREFIX . "membership WHERE id = '" . ( int )$membership_id . "'" );
    }

    public function getMembership( $membership_id ) {
        $query = $this->db->query( 'SELECT * FROM ' . DB_PREFIX . "membership WHERE id = '" . ( int )$membership_id . "'" );
        return $query->row;
    }

    public function getMemberships( $order = false ) {
        $sql =  'SELECT * FROM ' . DB_PREFIX . 'membership';
        if ( $order )
        $sql .= ' ORDER BY amount ASC';

        $query = $this->db->query( $sql );

        return $query->rows;
    }

    public function addMessage( $data ) {
        $this->db->query( 'INSERT INTO ' . DB_PREFIX . "firebase_messages (message, sent_at) VALUES ('" . $this->db->escape( $data ) . "', NOW())" );
    }

    public function getMessages() {
        $query = $this->db->query( 'SELECT * FROM ' . DB_PREFIX . 'firebase_messages' );
        return $query->rows;
    }

    public function addConfig( $data ) {
        $this->db->query( 'INSERT INTO ' . DB_PREFIX . "firebase_config (id, api_key, message_to_keep, message_promoted, message_demoted) VALUES ('1', '" . $this->db->escape( $data[ 'api' ] ) . "', '" . $this->db->escape( $data[ 'message_to_keep' ] ) . "', '" . $this->db->escape( $data[ 'message_promoted' ] ) . "', '" . $this->db->escape( $data[ 'message_demoted' ] ) . "')" );
    }

    public function getConfig() {
        $query = $this->db->query( 'SELECT * FROM ' . DB_PREFIX . 'firebase_config where id = 1' );
        return $query->row;
    }

    public function editConfig( $data ) {
        $query = $this->db->query( 'UPDATE ' . DB_PREFIX . "firebase_config SET api_key = '" . $this->db->escape( $data[ 'name' ] ) . "', message = '" . $this->db->escape( $data[ 'name' ] ) . "', where id = 1" );
        return $query->row;
    }

    public function sumRewardPointCustomer( $customer_ID ) {
        $query = $this->db->query( 'SELECT SUM(points) as points FROM ' . DB_PREFIX . 'customer_reward WHERE customer_id = ' . $customer_ID );
        return $query->row[ 'points' ];

    }
}
