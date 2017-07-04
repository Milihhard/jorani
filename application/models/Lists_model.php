<?php
/**
 * This Class contains all the business logic and the persistence layer for the
 * managing lists of employees. Each user can create and manage its own lists of
 * employees.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.6.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This Class contains all the business logic and the persistence layer for
 * custom lists of employees.
 */
class Lists_model extends CI_Model {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        
    }

    /**
     * Get the list of custom lists for an employee (often the connected user)
     * @param int $id identifier of a user owing the lists
     * @return array record of lists
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getLists($user) {
        $query = $this->db->get_where('org_lists', array('user' => $user));
        return $query->result_array();
    }
    
    /**
     * Insert a new list into the database
     * @param int $user User owning the list
     * @param string $name Name of the list
     * @return int last inserted id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function setLists($user, $name) {
        $data = array(
            'user' => $user,
            'name' => $name
        );
        $this->db->insert('org_lists', $data);
        return $this->db->insert_id();
    }

    /**
     * Update a given list in the database.
     * @param int $id identifier of the list
     * @param string $name name of the list
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function updateLists($id, $name) {
        $data = array(
            'name' => $name
        );
        $this->db->where('id', $id);
        return $this->db->update('org_lists', $data);
    }
    
    /**
     * Delete a list from the database
     * @param int $id identifier of the list
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteList($id) {
        $this->db->delete('org_lists', array('id' => $id));
    }

    
    /**
     * Get the list of employees for the given list identifier
     * @param int $id Identifier of the list of employees 
     * @return array record of employees
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getListOfEmployees($id) {
        $this->db->select('org_lists_employees.user as id');
        $this->db->select('firstname, lastname');
        $this->db->select('organization.name as entity');
        $this->db->from('org_lists');
        $this->db->join('org_lists_employees', 'org_lists_employees.list = org_lists.id');
        $this->db->join('users', 'users.id = org_lists_employees.user');
        $this->db->join('organization', 'organization.id = users.organization');
        $this->db->where('org_lists.id', $id);
        $this->db->order_by('org_lists_employees.orderlist');
        $query = $this->db->get();
        return $query->result_array();
    }
}
