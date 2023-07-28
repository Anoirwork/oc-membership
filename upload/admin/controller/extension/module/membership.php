<?php
/**
* @package       membership Social Login
* @copyright     Copyright 2011-2017 http://www.membership.com
* @license       GNU/GPL 2 or later
*
* This program is free software;
you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation;
either version 2
* of the License, or ( at your option ) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY;
without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program;
if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*
* The 'GNU General Public License' ( GPL ) is available at
* http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
*/

// ////////////////////////////////////////////////////////////////////
// Admin Panel
// ////////////////////////////////////////////////////////////////////

class ControllerExtensionModuleMembership extends Controller {
    private $error = array();
    // Settings Admin
    protected function index_settings( $data ) {
        // Section
        $data[ 'do_oa' ] = 'settings';

        $this->load->model( 'extension/module/membership' );
        ////////////////////////////////////////////////////////////////////////////////////////
        // Save Settings
        ////////////////////////////////////////////////////////////////////////////////////////

        if ( ( $this->request->server[ 'REQUEST_METHOD' ] == 'GET' ) && $this->validate() ) {
            $data[ 'memberships' ] = $this->model_extension_module_membership->getMemberships();
        }
        if ( ( $this->request->server[ 'REQUEST_METHOD' ] == 'POST' ) && $this->validate() ) {
            $this->model_extension_module_membership->addMembership( $this->request->post );
            $data[ 'memberships' ] = $this->model_extension_module_membership->getMemberships();
            if ( $this->db->getLastId() ) {
                $this->load->language( 'extension/module/membership' );
                $data[ 'mm_success_message' ] = $this->language->get( 'mm_text_settings_saved' );
            }
        }

        // Done

        return $data;
    }

    // fb_config Admin
    protected function index_fb_config( $data ) {
        // Section
        $data[ 'do_oa' ] = 'fb_config';

        $this->load->model( 'extension/module/membership' );
        ////////////////////////////////////////////////////////////////////////////////////////
        // Remove Position
        ////////////////////////////////////////////////////////////////////////////////////////
        if ( ( $this->request->server[ 'REQUEST_METHOD' ] == 'GET' ) && $this->validate() ) {
            // Add Position
            if ( isset( $this->request->get ) && is_array( $this->request->get ) ) {
                // Remove this position
                $data[ 'config' ] = $this->model_extension_module_membership->getConfig();
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        // Save Settings
        ////////////////////////////////////////////////////////////////////////////////////////
        if ( ( $this->request->server[ 'REQUEST_METHOD' ] == 'POST' ) && $this->validate() ) {
            // Add Position

            // Redirect

        }

        ////////////////////////////////////////////////////////////////////////////////////////
        // Default data
        ////////////////////////////////////////////////////////////////////////////////////////

        // Read fb_config

        // Done

        return $data;
    }

    protected function index_fb_messages( $data ) {
        $data[ 'do_oa' ] = 'messages';
        ////////////////////////////////////////////////////////////////////////////////////////
        // Save Settings
        ////////////////////////////////////////////////////////////////////////////////////////

        if ( ( $this->request->server[ 'REQUEST_METHOD' ] == 'GET' ) && $this->validate() ) {

        }

        // Done

        return $data;
    }

    // Display Admin

    public function index() {
        // Language
        $data = $this->load->language( 'extension/module/membership' );
        // What do we need to do?
        $do = ( !empty( $this->request->get[ 'do' ] ) ? $this->request->get[ 'do' ] : 'settings' );

        // Page Title
        $this->document->setTitle( $this->language->get( 'heading_title' ) );

        // Load Models
        $this->load->model( 'setting/setting' );
        $this->load->model( 'design/layout' );

        // BreadCrumbs
        $data[ 'breadcrumbs' ] = array(
            array(
                'text' => $this->language->get( 'text_home' ),
                'href' => $this->url->link( 'common/dashboard', 'user_token=' . $this->session->data[ 'user_token' ],
                true ),
                'separator' => false
            ),
            array(
                'text' => $this->language->get( 'text_extension' ),
                'href' => $this->url->link( 'extension/extension', 'user_token=' . $this->session->data[ 'user_token' ],
                true ),
                'separator' => ' :: '
            ),
            array(
                'text' => $this->language->get( 'heading_title' ),
                'href' => $this->url->link( 'extension/module/membership',
                'user_token=' . $this->session->data[ 'user_token' ], true ),
                'separator' => ' :: '
            )
        );

        // Buttons
        $data[ 'action' ] = $this->url->link( 'extension/module/membership',
        'user_token=' . $this->session->data[ 'user_token' ], true );
        $data[ 'cancel' ] = $this->url->link( 'extension/module/membership',
        'user_token=' . $this->session->data[ 'user_token' ], true );

        // Add Settings
        $data = array_merge( $data, $this->model_setting_setting->getSetting( 'module_membership' ) );

        // What to show
        $do = ( !empty( $this->request->get[ 'do' ] ) && $this->request->get[ 'do' ] == 'fb_config' ) ? 'fb_config' : ( ( !empty( $this->request->get[ 'do' ] ) && $this->request->get[ 'do' ] == 'messages' ) ? 'messages' : 'settings' );

        // Show fb_config
        if ( $do == 'fb_config' ) {
            $data = $this->index_fb_config( $data );
        }
        // Show Settings
        else if ( $do == 'settings' ) {
            $data = $this->index_settings( $data );
        }

        // Show messages
        else if ( $do == 'messages' ) {
            $data = $this->index_fb_messages( $data );
        }

        // Settings Saved
        if ( isset( $this->request->get ) && !empty( $this->request->get[ 'mm_action' ] ) == 'saved' ) {
            $data[ 'mm_success_message' ] = $data[ 'mm_text_settings_saved' ];
        }

        // Error Message
        if ( !empty( $this->error[ 'warning' ] ) ) {
            $data[ 'mm_error_message' ] = $this->error[ 'warning' ];
        }

        $data[ 'header' ] = $this->load->controller( 'common/header' );
        $data[ 'column_left' ] = $this->load->controller( 'common/column_left' );
        $data[ 'footer' ] = $this->load->controller( 'common/footer' );
        $data[ 'user_token' ] = $this->session->data[ 'user_token' ];
        // Display Page
        $this->response->setOutput( $this->load->view( 'extension/module/membership', $data ) );
    }

    // Validation

    private function validate() {
        // Can this user modify the settings?
        if ( !$this->user->hasPermission( 'modify', 'extension/module/membership' ) ) {
            $this->error[ 'warning' ] = $this->language->get( 'mm_text_error_permission' );

            return false;
        }

        // Done

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Installer
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    // Installation Script

    public function install() {
        // Create membership table
        $sql = 'CREATE TABLE IF NOT EXISTS ' . DB_PREFIX . "membership (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name VARCHAR(50) NOT NULL,
            amount FLOAT(11) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        );
        ";
        $this->db->query( $sql );
        $sql = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . "firebase_config` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `server_key` TEXT NOT NULL,
            `sender_id` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ";
        $this->db->query( $sql );
        $sql = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . "firebase_messages` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `message` TEXT NOT NULL,
            `sent_at` DATETIME,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ";
        $this->db->query( $sql );

        $this->db->query( 'INSERT INTO ' . DB_PREFIX . "membership (`id`, `name`, `amount`, `created_at`, `updated_at`) VALUES 
            (NULL, 'Silver',    '1000', current_timestamp(), current_timestamp()),
            (NULL, 'Gold',      '1500', current_timestamp(), current_timestamp()),
            (NULL, 'Platinum',  '2000', current_timestamp(), current_timestamp()),
        " );

        $this->db->query( 'INSERT INTO `' . DB_PREFIX . "firebase_config` (`id`, `api_key`, `message_to_keep`, `message_promoted`, `message_demoted`, `created_at`, `updated_at`) VALUES 
        (1, 'TOKEN PLACEHOLDER PLEASE REPLACE IT WITH YOURS', 'Hey %s you had just received a reward of %s.to be promoted to the next tier %s you need to earn %s!', 'Congratulation %s ! You have been promoted to %s.', 'Bad news %s! You had been demoted to %s.')
        ;
        " );

    }

    // UnInstallation Script

    public function uninstall() {
        // Force Remove
        $force_remove = false;

        // These table should normally not be dropped, otherwise the customers can no longer login if the webmaster re-installs the extension.
        if ( $force_remove === true ) {
            // Membership table
            $sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'membership`;';
            $this->db->query( $sql );
            $sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'firebase_messages`;';
            $this->db->query( $sql );
            $sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'firebase_config`;';
            $this->db->query( $sql );
        }

        foreach ( $this->getEvents() as $code => $event ) {
            $this->model_setting_event->deleteEvent( $code );
        }
    }

    /**
    * Returns events configuration list used by the module
    *
    * @return array
    */

    private function getEvents() {
        $events = [
            // update data
            'membership_view_before' => [
                'trigger' => 'catalog/view/*/before',
                'action' => 'extension/module/membership/twig'
            ]
        ];

        return $events;
    }

    public function removeMembership() {
        $json = array();
        $this->load->model( 'extension/module/membership' );
        if ( isset( $this->request->post[ 'membership_id' ] ) ) {
            $this->model_extension_module_membership->deleteMembership( $this->request->post[ 'membership_id' ] );
            $json[ 'success' ] = true;
        }

        $this->response->addHeader( 'Content-Type: application/json' );
        $this->response->setOutput( json_encode( $json ) );
    }

    public function saveFbConfig() {
        $json = array();
        $this->load->model( 'extension/module/membership' );
        if ( ( $this->request->server[ 'REQUEST_METHOD' ] == 'POST' ) && $this->validate() ) {
            // Add Position
            if ( isset( $this->request->post ) && is_array( $this->request->post ) ) {
                // Remove this position
                $this->model_extension_module_membership->addConfig( $this->request->post );
                $json[ 'success' ] = true;
            }
        }

        $this->response->addHeader( 'Content-Type: application/json' );
        $this->response->setOutput( json_encode( $json ) );
    }

    public function editMembership() {
        $json = array();
        $this->load->model( 'extension/module/membership' );
        if ( isset( $this->request->post[ 'membership_id' ] ) ) {

            $this->model_extension_module_membership->editMembership( $this->request->post );
            $json[ 'body' ] = $this->request->post;
            $json[ 'success' ] = true;
            $json[ 'name' ] = $this->request->post[ 'name' ];
            $json[ 'amount' ] = $this->request->post[ 'amount' ];

        }

        $this->response->addHeader( 'Content-Type: application/json' );
        $this->response->setOutput( json_encode( $json ) );
    }

    public function send_notification( $title, $body, $customer, $fb_config ) {
        $this->load->model( 'extension/module/membership' );

        // Replace these with your Firebase configuration
        $serverKey = $fb_config[ 'api_key' ];

        // The notification payload
        $notification = [
            'title' => $title,
            'body' => $body,
        ];

        // Prepare the message data
        $messageData = [
            'to' => $customer[ 'token' ],
            'notification' => $notification,
            'priority' => 'high',
        ];

        // Convert the message data to JSON
        $jsonMessage = json_encode( $messageData );

        // Set the headers for the request
        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ];

        // Send the request to Firebase Cloud Messaging
        $ch = curl_init( 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $jsonMessage );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $response = curl_exec( $ch );
        curl_close( $ch );

        // Handle the response from Firebase
        $data = json_decode( $response, true );

        if ( isset( $data[ 'message_id' ] ) ) {
            // The message was sent successfully
            $this->session->data[ 'success' ] = $this->language->get( 'text_success' );

            $this->load->model( 'extension/module/membership' );
            $this->model_extension_module_membership->addMessage( $body );
        } else {
            // There was an error sending the message
            $this->session->data[ 'error' ] = $this->language->get( 'text_error' );
        }

        // Redirect back to the appropriate page
        //$this->response->redirect( $this->url->link( 'common/dashboard', 'user_token=' . $this->session->data[ 'user_token' ], true ) );
    }
    /**
    * process notification
    *
    * @param [ type ] $customer_id
    * @param [ type ] $newly_points
    * @return array
    */

    public function process( $customer_id, $newly_points ) {
        $this->load->model( 'extension/module/membership' );
        $this->load->model( 'customer/customer' );
        $points_total = $this->model_extension_module_membership->sumRewardPointCustomer( $customer_id );
        $points_before_reward = $points_total - $newly_points;
        $memberships = $this->model_extension_module_membership->getMemberships( true );
        $current_membership = '';
        $membership_name = '';
        $fb_configuration = $this->model_extension_module_membership->getConfig();
        $customer_info = $this->model_customer_customer->getCustomer( $customer_id );
        $i_promotion = null;
        $i_current = null;
        $promotion_membership = '';
        //To keep your current membership %s you need to earn %s points and if you want to be promoted to the next membership you need to spend %s!
        //Hey %s you had just received a reward of %s. To be promoted to the next tier %s you need to earn %s!
        if ( ( int ) $points_total < ( int ) $memberships[ 0 ][ 'amount' ] ) {
            $this->send_notification( 'Membership Update',
            sprintf( $fb_configuration[ 'message_to_keep' ], $customer_info[ 'firstname' ] . ' ' . $customer_info[ 'lastname' ], $newly_points, $memberships[ 0 ][ 'name' ], ( int ) $memberships[ 0 ][ 'amount' ] - ( int ) $points_total ),
            $customer_info, $fb_configuration );
            return;
        }
        for ( $i = 0; $i < count( $memberships ) ;
        $i++ ) {

            if ( $points_total  >= $memberships[ $i ][ 'amount' ] && $points_total <= $memberships[ $i++ ][ 'amount' ] ) {
                $membership_name = $memberships[ $i ][ 'name' ];
                $i_promotion = $i;
            }
            if ( $points_before_reward  >= $memberships[ $i ][ 'amount' ] && $points_before_reward <= $memberships[ $i++ ][ 'amount' ] ) {
                $current_membership = $memberships[ $i ][ 'name' ];
                $i_current = $i;
            }

        }
        if ( $i_promotion != null && isset( $memberships[ $i_promotion ] ) ) {
            $promotion_membership = $memberships[ $i_promotion ];
        }
        if ( $i_current != null && isset( $memberships[ $i_current ] ) ) {
            $current_membership_info = $memberships[ $i_current ];
        }

        if ( $membership_name == $current_membership ) {
            //for promoting to the next membership
            $this->send_notification( 'Keep your membership',
            sprintf( $fb_configuration[ 'message_to_keep' ],
            $memberships[ $i_current ][ 'name' ],
            ( int )  $points_total,
            ( int ) $points_total - ( int )$memberships[ $i_current ][ 'amount' ] ),
            $customer_info,
            $fb_configuration );
            return;
        }
        if ( $membership_name != $current_membership ) {
            $this->send_notification( 'Congratulations', sprintf( $fb_configuration[ 'message_promoted' ], $customer_info[ 'firstname' ] . ' ' . $customer_info[ 'lastname' ], $membership_name, '123' ), $customer_info, $fb_configuration );

            return;
        }
        return [ sprintf( $fb_configuration[ 'message_promoted' ], $customer_info[ 'firstname' ] . ' ' . $customer_info[ 'lastname' ], $membership_name, ), $customer_info, $fb_configuration ];
    }
}
