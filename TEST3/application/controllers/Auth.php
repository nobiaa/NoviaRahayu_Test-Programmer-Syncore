<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('login');
    }

    public function process()
    {
        $post = $this->input->post(null, TRUE);
        if (isset($post['login'])) {
            $this->load->model('user_m');
            $query = $this->user_m->login($post);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $params = array(
                    'id_user' => $row->id_user,
                    'level' => $row->level
                );
                $this->session->set_userdata($params);
                if ($this->session->userdata('level') == 1) {
                    echo "<script>
                    alert('Selamat Login Berhasil')
                    window.location = '" . site_url('user/Beranda') . "';
                </script>";
                }
            } else {
                echo "<script>
                    alert('Login Gagal')
                    window.location = '" . site_url('auth/index') . "';
                    </script>";
            }
        }
    }

    //Login
    public function login()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');


        if ($this->form_validation->run() == false) {
            // $data['title'] = 'Login Page';
            // $this->load->view('templates/auth_header', $data);
            // $this->load->view('auth/login');
            // $this->load->view('templates/auth_footer');
            $data = array(
                'judul' => 'Edit Data User'
            );
            redirect('auth');
        } else {
            // validasi sukses
            //membuat method login mode privat
            $this->_login();
        }
    }

    private function _login()
    {
        $username = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        if ($user) {
            //usernya ada
            //if ($user['is_active'] == 1) {
            //cek password
            if (password_verify($password, $user['password'])) {
                // $data = [
                //     'email' => $user['email'],
                //     'role_id' => $user['role_id']
                // ];
                // $this->session->set_userdata($data);
                // redirect('user');
                $params = [
                    'id_user' => $user['id_user']
                ];
                $this->session->set_userdata($params);
                if ($this->session->userdata('level') == 1) {
                    echo "<script>
                    alert('Selamat Login Berhasil')
                    window.location = '" . site_url('user/Beranda') . "';
                </script>";
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert-danger role="alert">password salah</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert-danger"
            role="alert">Login gagal</div>');
            redirect('auth');
        }
    }



    public function logout()
    {
        $params = array('id_user');
        $this->session->unset_userdata($params);
        redirect('auth/index');
    }
}
