<?php

class Model_mapel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * get all data
     *
     * @param int $limit
     * @param int $offset
     * @param array $filter
     * @return array
     */
    public function get_all(int $limit = NULL, int $offset = NULL, array $filter = NULL): array {

        if(!empty($limit) && !is_null($offset))
            $this->db->limit($limit, $offset);

        $this->db->select('a.*, b.subject_name')
                 ->join('a.subject_id=b.subject_id');
        $get = $this->db->get('materi a');

        return $get->result_array() ?? [];
    }

    /**
     * Num rows with filter
     *
     * @param array $filter
     * @return integer
     */
    public function num_all(array $filter = NULL): int {
        
        $this->db->select('a.*, b.subject_name')
                 ->join('a.subject_id=b.subject_id');
        $get = $this->db->get('materi a');

        return $get->num_rows() ?? 0;
    }
}