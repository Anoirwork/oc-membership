<?php
// ////////////////////////////////////////////////////////////////////
// Admin Panel
// ////////////////////////////////////////////////////////////////////

class ControllerExtensionModuleMembership extends Controller
{
    private $error = array();
    // Settings Admin
    protected function index_settings($data)
    {
        // Section
        $data['do_oa'] = 'settings';

        $this->load->model('extension/module/membership');
        ////////////////////////////////////////////////////////////////////////////////////////
        // Save Settings
        ////////////////////////////////////////////////////////////////////////////////////////

        if (($this->request->server['REQUEST_METHOD'] == 'GET') && $this->validate()) {
            $data['memberships'] = $this->model_extension_module_membership->getMemberships();
        }
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate() && isset($this->request->post['area']) && $this->request->post['area'] == 'settings') {
            //$data['post'] = $this->request->post;
            //trigger_error(print_r($this->request->post));
            //$this->model_extension_module_membership->addNewMembership( $this->request->post );
            $formData = $this->request->post;

            // Get the number of elements in the arrays (assuming they have the same length)
            $numElements = count($formData['name']);

            // Iterate through each index
            for ($i = 0; $i < $numElements; $i++) {
                $name = $formData['name'][$i];
                $amount = $formData['amount'][$i];
                $discount = $formData['discount'][$i];
                if (!empty($name) && !empty($amount) && !empty($discount)) {
                    // Process or insert the data as needed
                    $this->model_extension_module_membership->addNewMembership(array(
                        'name'     => $name,
                        'amount'   => $amount,
                        'discount' => $discount
                    ));
                } else {
                    // Handle the case where any of the values is empty
                    $this->log->write('Warning: Empty values not processed for index ' . $i);
                }

                // You can also use $this->log or trigger_error for debugging if needed
            }

            $data['memberships'] = $this->model_extension_module_membership->getMemberships();
            if ($this->db->getLastId()) {
                $data['mm_success_message'] = $this->language->get('mm_text_settings_saved');
            }
        }
        return $data;
    }

    // fb_config Admin
    protected function index_fb_config($data)
    {
        // Section
        $data['do_oa'] = 'fb_config';

        $this->load->model('extension/module/membership');
        ////////////////////////////////////////////////////////////////////////////////////////
        // Remove Positon
        ////////////////////////////////////////////////////////////////////////////////////////
        if (($this->request->server['REQUEST_METHOD'] == 'GET') && $this->validate()) {
            // Add Position
            if (isset($this->request->get) && is_array($this->request->get)) {
                // Remove this position
                $data['config'] = $this->model_extension_module_membership->getConfig();
                $data['sent_notification_count'] = $this->model_extension_module_membership->getSentNotificaitonCount();
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        // Save Settings
        ////////////////////////////////////////////////////////////////////////////////////////
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate() && isset($this->request->post['area']) && $this->request->post['area'] == 'fb_config') {
            // Add Position
            $this->request->post['expiration_duration'] = $this->request->post['duration_number'] . ' ' . $this->request->post['duration_unit'];
            $this->request->post['expiration_criteria'] = $this->request->post['exp_criteria'] . ' ' . $this->request->post['exp_criteria_value'];
            $this->model_extension_module_membership->addConfig($this->request->post);
            //trigger_error(print_r($this->request->post));
            $data['config'] = $this->model_extension_module_membership->getConfig();
            if ($this->db->getLastId()) {
                $data['mm_success_message'] = $this->language->get('mm_text_settings_saved');
            }
            // Redirect

        }

        ////////////////////////////////////////////////////////////////////////////////////////
        // Default data
        ////////////////////////////////////////////////////////////////////////////////////////

        // Read fb_config

        // Done

        return $data;
    }

    protected function index_fb_messages($data)
    {
        $data['do_oa'] = 'messages';
        ////////////////////////////////////////////////////////////////////////////////////////
        // Save Settings
        ////////////////////////////////////////////////////////////////////////////////////////

        if (($this->request->server['REQUEST_METHOD'] == 'GET') && $this->validate()) {
            $this->load->model('extension/module/membership');
            $data['messages'] = $this->model_extension_module_membership->getMessages();
        }

        // Done

        return $data;
    }

    // Display Admin

    public function index()
    {
        // Language
        $data = $this->load->language('extension/module/membership');
        $data['mm_success_message'] = "";
        // What do we need to do?
        $do = (!empty($this->request->get['do']) ? $this->request->get['do'] : 'settings');

        // Page Title
        $this->document->setTitle($this->language->get('heading_title'));

        // Load Models
        $this->load->model('setting/setting');
        $this->load->model('design/layout');

        // BreadCrumbs
        $data['breadcrumbs'] = array(
            array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link(
                    'common/dashboard',
                    'user_token=' . $this->session->data['user_token'],
                    true
                ),
                'separator' => false
            ),
            array(
                'text' => $this->language->get('text_extension'),
                'href' => $this->url->link(
                    'extension/extension',
                    'user_token=' . $this->session->data['user_token'],
                    true
                ),
                'separator' => ' :: '
            ),
            array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link(
                    'extension/module/membership',
                    'user_token=' . $this->session->data['user_token'],
                    true
                ),
                'separator' => ' :: '
            )
        );

        // Buttons
        $data['action'] = $this->url->link(
            'extension/module/membership',
            'user_token=' . $this->session->data['user_token'],
            true
        );
        $data['cancel'] = $this->url->link(
            'extension/module/membership',
            'user_token=' . $this->session->data['user_token'],
            true
        );

        // Add Settings
        $data = array_merge($data, $this->model_setting_setting->getSetting('module_membership'));

        // What to show
        //$do = ( !empty( $this->request->get[ 'do' ] ) && $this->request->get[ 'do' ] == 'fb_config' ) ? 'fb_config' : ( ( !empty( $this->request->get[ 'do' ] ) && $this->request->get[ 'do' ] == 'messages' ) ? 'messages' : 'settings' );
        if (!empty($this->request->get['do']) && $this->request->get['do'] == 'fb_config') {
            $do = 'fb_config';
        } elseif (!empty($this->request->get['do']) && $this->request->get['do'] == 'messages') {
            $do = 'messages';
        } elseif (!empty($this->request->get['do']) && $this->request->get['do'] == 'settings' && $this->request->server['REQUEST_METHOD'] != 'POST') {
            $do = 'settings';
        } else {
            $do = ($this->request->server['REQUEST_METHOD'] == 'GET') ? 'settings' : '';
        }

        // Show fb_config
        if ($do == 'fb_config') {
            $data = $this->index_fb_config($data);
        }
        // Show messages
        else if ($do == 'messages') {
            $data = $this->index_fb_messages($data);
        }
        // Show settings
        else {
            $data = $this->index_settings($data);
        }

        // Settings Saved
        if (isset($this->request->get) && !empty($this->request->get['mm_action']) == 'saved') {
            $data['mm_success_message'] = $data['mm_text_settings_saved'];
        }

        // Error Message
        if (!empty($this->error['warning'])) {
            $data['mm_error_message'] = $this->error['warning'];
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['user_token'] = $this->session->data['user_token'];
        // Display Page
        $this->response->setOutput($this->load->view('extension/module/membership', $data));
    }

    // Validation

    private function validate()
    {
        // Can this user modify the settings?
        if (!$this->user->hasPermission('modify', 'extension/module/membership')) {
            $this->error['warning'] = $this->language->get('mm_text_error_permission');

            return false;
        }

        // Done

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Installer
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    // Installation Script

    public function install()
    {
        // Create membership table
        $sql = 'CREATE TABLE IF NOT EXISTS ' . DB_PREFIX . "membership (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name VARCHAR(50) NOT NULL,
            amount FLOAT(11) NOT NULL,
            discount INT(11),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        );
        ";
        $this->db->query($sql);
        $sql = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'firebase_config` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `api_key` text NOT NULL,
            `message_novice` text NOT NULL,
            `message_keep` text NOT NULL,
            `message_promoted` text NOT NULL,
            `message_demoted` text NOT NULL,
            `expiration_duration` text NOT NULL,
            `minimum_points` int(11) NOT NULL,
            `expiration_criteria` text NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
        $this->db->query($sql);
        $sql = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . "firebase_messages` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `message` TEXT NOT NULL,
            `sent_at` DATETIME,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ";
        $this->db->query($sql);
        $sql = "
        INSERT INTO `" . DB_PREFIX . "firebase_config` (`id`, `api_key`, `message_novice`, `message_keep`, `message_promoted`, `message_demoted`, `expiration_duration`, `minimum_points`, `expiration_criteria`) VALUES
            (1, 'AAAASpAxHmw:APA91bFz1ecYiCQArlK0ORdrOfppQm7O-jcvASMlTPtzUkn6y6vEW26g0bv-u17VwmhDbFlLTDrihgcL66HrWIuwyMJBBFwZFQbrLF7UuiVlJA-jam5VYoWUIsW7YpOhgtvq2NwL2sQQ', 
            'Hey %s you had just received a reward of %s, you have a total of %s. To be promoted to the next tier %s you need to earn %s!', 
            'Hey %s you had just received a reward of %s, you have a total of %s. To keep your current memebership you need to spend %s before %s!', 
            'Congratulation %s ! You have been promoted to %s.',
            'Bad news %s ! You have been demoted to %s.',
            '3 Months',
            700,
            'Spend 1000$'
        );
        ";
        $this->db->query($sql);
        $sql = "
        CREATE TABLE IF NOT EXISTS ext_customer_membership (
            customer_membership_id INT PRIMARY KEY AUTO_INCREMENT,
            customer_id INT,
            membership_id INT,
            total_reward_points INT,
            created_at DATETIME,
            updated_at DATETIME
        );
        ";
        $this->db->query($sql);
    }

    // UnInstallation Script

    public function uninstall()
    {
        // Membership table
        $sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'membership`;';
        $this->db->query($sql);
        $sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'firebase_messages`;';
        $this->db->query($sql);
        $sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . 'firebase_config`;';
        $this->db->query($sql);
        $sql = 'DROP TABLE IF EXISTS `ext_customer_membership`;';
        $this->db->query($sql);
    }


    public function removeMembership()
    {
        $json = array();
        $this->load->model('extension/module/membership');
        if (isset($this->request->post['membership_id'])) {
            $this->model_extension_module_membership->deleteMembership($this->request->post['membership_id']);
            $json['success'] = true;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function saveFbConfig()
    {
        $json = array();
        $this->load->model('extension/module/membership');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            // Add Position
            if (isset($this->request->post) && is_array($this->request->post)) {
                // Remove this position
                $this->model_extension_module_membership->addConfig($this->request->post);
                $json['success'] = true;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function editMembership()
    {
        $json = array();
        $this->load->model('extension/module/membership');
        if (isset($this->request->post['membership_id'])) {

            $this->model_extension_module_membership->editMembership($this->request->post);
            $json['body'] = $this->request->post;
            $json['success'] = true;
            $json['name'] = $this->request->post['name'];
            $json['amount'] = $this->request->post['amount'];
            $json['discount'] = $this->request->post['discount'];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function send_notification($title, $body, $customer, $fb_config, $membership_id, $new_pts)
    {
        $this->load->model('extension/module/membership');

        $serverKey = $fb_config['api_key'];

        $notification = [
            'title' => $title,
            'body' => $body,
        ];

        $messageData = [
            'to' => $customer['token'],
            'notification' => $notification,
            'priority' => 'high',
        ];

        $jsonMessage = json_encode($messageData);

        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init('https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonMessage);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['results'][0]['message_id'])) {
            $this->load->model('extension/module/membership');
            $this->model_extension_module_membership->addMessage($body);
            $this->model_extension_module_membership->updateCustomerMembership($customer['customer_id'], $membership_id, $new_pts);
            $this->session->data['success'] = $this->language->get('text_success');
        } else {
            $this->session->data['error'] = $this->language->get('text_error');
        }
    }

    /**
     * process notification
     *
     * @param [ type ] $customer_id
     * @param [ type ] $newly_points
     * @return array
     */

    public function process($customer_id, $newly_points)
    {
        $this->load->model('extension/module/membership');
        $this->load->model('customer/customer');

        $fb_configuration = $this->model_extension_module_membership->getConfig();
        if ($newly_points > $fb_configuration['minimum_points']) { // Minimum reward points to make this function continue processing

            $customer_info = $this->model_customer_customer->getCustomer($customer_id);

            $points_total = $this->model_extension_module_membership->sumRewardPointCustomer($customer_id);
            $points_before_reward = $points_total - $newly_points;

            $memberships = $this->model_extension_module_membership->getMemberships(true);
            $first_time_notification = $this->model_extension_module_membership->isFirstTimeReward($customer_id);
            $promotion_membership = null;
            $current_membership_info = null;

            $customer_full_name = $customer_info['firstname'] . ' ' . $customer_info['lastname'];

            if ($first_time_notification && $points_total < $memberships[0]['amount']) { //For novice 
                $this->send_notification(
                    'Membership Update',
                    sprintf(
                        $fb_configuration['message_novice'],
                        $customer_full_name,
                        $newly_points,
                        $points_total,
                        $memberships[0]['name'],
                        (int) $memberships[0]['amount'] - (int) $points_total
                    ),
                    $customer_info,
                    $fb_configuration,
                    $membership[0]['id'],
                    $newly_points
                );
                return [];
            }

            for (
                $i = count($memberships) - 1;
                $i >= 0;
                $i--
            ) {
                if ($points_total >= $memberships[$i]['amount']) {
                    $promotion_membership = $memberships[$i];
                    break;
                }
            }

            for (
                $i = count($memberships) - 1;
                $i >= 0;
                $i--
            ) {
                if ($points_before_reward >= $memberships[$i]['amount'] && (isset($memberships[$i + 1]) && $points_before_reward < $memberships[$i + 1]['amount'])) {
                    $current_membership_info = $memberships[$i];
                    break;
                }
            }

            if ($current_membership_info && $promotion_membership && $current_membership_info['name'] == $promotion_membership['name']) {
                $date = $this->model_extension_module_membership->getLastReward($customer_id);
                $this->send_notification(
                    'Keep your membership',
                    sprintf(
                        $fb_configuration['message_keep'],
                        $customer_full_name,
                        $newly_points,
                        $points_total,
                        $fb_configuration['minimum_points'],
                        date('d/m/Y', strtotime($date . ' + ' . $fb_configuration['expiration_duration']))
                    ),
                    $customer_info,
                    $fb_configuration,
                    $current_membership_info['id'],
                    $newly_points
                );
                return [];
            } elseif ($promotion_membership) {
                $this->send_notification(
                    'Congratulations',
                    sprintf(
                        $fb_configuration['message_promoted'],
                        $customer_full_name,
                        $promotion_membership['name']
                    ),
                    $customer_info,
                    $fb_configuration,
                    $promotion_membership['id'],
                    $newly_points
                );
                return [];
            }
            return [];
        }
        return [];
    }

    
}