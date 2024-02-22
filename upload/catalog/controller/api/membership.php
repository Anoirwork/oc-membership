<?php
class ControllerApiMembership extends Controller
{
    public function get_membership_data()
    {
        if ($this->customer->isLogged()) {
            $customer_id = $this->customer->getId();
            $query = $this->db->query("SELECT
                (SELECT name FROM och6_membership mm WHERE mm.id = ext.membership_id ORDER BY mm.id ASC LIMIT 1) AS current_mm,
                (SELECT amount FROM och6_membership mm WHERE mm.id = ext.membership_id ORDER BY mm.id ASC LIMIT 2) AS next_mm_pts, 
                total_reward_points, updated_at FROM ext_customer_membership ext WHERE customer_id =" . $customer_id);
            $membershipData = $query->row;

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($membershipData));
        } else {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(['success' => 0, 'error' => 'Invalid customer data']));
        }
    }
}
