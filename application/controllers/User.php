<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        // check login
        check_not_login();
        check_admin();

        $this->load->model('user_m');
        $this->load->library('form_validation');
    }

    public function index()
    {

        $data['row'] = $this->user_m->get();
        // load user data
        $this->template->load('template', 'user/user_data', $data);
    }

    public function add()
    {
        // falidasi form
   
        $this->form_validation->set_rules('fullname', 'Nama', 'required');
        //  minimal karakter 5
        // is_unique digunakan agar username tidak ada yang sama is_unique[table.nama kolom tabel]
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|is_unique[user.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
        // matches fungsi agar password harus sama
        $this->form_validation->set_rules(
            'passconf',
            'Konfirmasi_password',
            'required|matches[password]',
            array('matches' => '%s tidak sesuai dengan password')
        );
        $this->form_validation->set_rules('level', 'Level', 'required');

        // fungsi validation dari ci3
        $this->form_validation->set_message('required', '%s kolom kosong,mohon di isi');
        $this->form_validation->set_message('min_length', '{field} minimal 5 karakter');
        $this->form_validation->set_message('is_unique', '{field} nama ini sudah terpakai');


        if ($this->form_validation->run() == FALSE) {
            $this->template->load('template', 'user/user_form_add');
        } else {
            $post = $this->input->post(null, TRUE);
            $this->user_m->add($post);
            //    alert berhasil ditambah data user dan gagal ditambah data user
            if ($this->db->affected_rows() > 0) {
                echo "<script>
             alert('Data berhasil di simpan');
            </script>";
            }
            echo "<script>
            window.location='" . site_url('user') . "';
           </script>";
        }
    }

    public function del()
    {
        $id = $this->input->post('user_id');
        $this->user_m->del($id);
        if ($this->db->affected_rows() > 0) {
            echo "<script>
         alert('Data berhasil di hapus');
        </script>";
        }
        echo "<script>
        window.location='" . site_url('user') . "';
       </script>";
    }


    public function edit($id)
    {
        // falidasi form
      
        $this->form_validation->set_rules('fullname', 'Nama', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|callback_username_check');
        //  minimal karakter 5
        // is_unique digunakan agar username tidak ada yang sama is_unique[table.nama kolom tabel]
        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[5]');
            // matches fungsi agar password harus sama
            $this->form_validation->set_rules(
                'passconf',
                'Konfirmasi_password',
                'matches[password]',
                array('matches' => '%s tidak sesuai dengan password')
            );
        }
        if ($this->input->post('passconf')) {

            // matches fungsi agar password harus sama
            $this->form_validation->set_rules(
                'passconf',
                'Konfirmasi_password',
                'matches[password]',
                array('matches' => '%s tidak sesuai dengan password')
            );
        }

        $this->form_validation->set_rules('level', 'Level', 'required');

        // fungsi validation dari ci3
        $this->form_validation->set_message('required', '%s kolom kosong,mohon di isi');
        $this->form_validation->set_message('min_length', '{field} minimal 5 karakter');
        $this->form_validation->set_message('is_unique', '{field} nama ini sudah terpakai');


        if ($this->form_validation->run() == FALSE) {
            $query = $this->user_m->get($id);
            if ($query->num_rows() > 0) {
                $data['row'] = $query->row();
                $this->template->load('template', 'user/user_form_edit', $data);
            } else {
                echo "<script>
             alert('Data tidak ditemukan');
            ";
                echo "
            window.location='" . site_url('user') . "';
           </script>";
            }
        } else {
            $post = $this->input->post(null, TRUE);

            $this->user_m->edit($post);
            //    alert berhasil ditambah data user dan gagal ditambah data user
            if ($this->db->affected_rows() > 0) {
                echo "<script>
             alert('Data berhasil di simpan');
            </script>";
            }
            echo "<script>
            window.location='" . site_url('user') . "';
           </script>";
        }
    }
    function username_check(){
        $post = $this->input->post(null, TRUE);
        // jadi select tabel user yang username nya sama dengan username dan user id nya tidak sama dengan inputan user_id
        $query = $this->db->query("SELECT * FROM user where username = '$post[username]' AND user_id != '$post[user_id]'");
        if ($query->num_rows() > 0){
            $this->form_validation->set_message('username_check', '{field} ini sudah terpakai,silahkan ganti');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}
