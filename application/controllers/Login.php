<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function index()
    {
        $this->load->view('login/v_login');
    }

    public function proses_login()
    {
        // Validasi form input
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal, kembali ke halaman login
            $this->session->set_flashdata('login_gagal', 'Username dan Password wajib diisi!');
            redirect('login');
        } else {
            // Ambil input dari form
            $username = $this->input->post('username');
            $password = $this->input->post('password');  // Jangan hash password di sini

            $login = [
                'username' => $username,
            ];

            // Pengecekan data ke table petugas
            $cek_login = $this->M_login->get_data('petugas', $login);
            // Pengecekan data ke table siswa
            $cek_login_siswa = $this->M_login->get_data('siswa', $login);

            // Jika login sebagai admin/petugas
            if ($cek_login->num_rows() > 0) {
                // Ambil data satu baris petugas
                $row = $cek_login->row();

                // Verifikasi password menggunakan password_verify()
                if (password_verify($password, $row->password)) {
                    // Buat session
                    $sess_admin = array(
                        'id_petugas' => $row->id_petugas,
                        'username' => $row->username,
                        'level' => $row->level,
                    );
                    $this->session->set_userdata($sess_admin);

                    // Redirect sesuai level
                    if ($this->session->userdata('level') == "admin") {
                        redirect('dashboard-admin');
                    } elseif ($this->session->userdata('level') == "petugas") {
                        redirect('dashboard-petugas');
                    }
                } else {
                    $this->session->set_flashdata('login_gagal', 'Password Salah!');
                    redirect('login');
                }
            }
            // Jika login sebagai siswa
            elseif ($cek_login_siswa->num_rows() > 0) {
                // Ambil data satu baris pada table siswa
                $row_siswa = $cek_login_siswa->row();

                // Verifikasi password untuk siswa
                if (password_verify($password, $row_siswa->password)) {
                    // Buat session
                    $sess_siswa = array(
                        'nisn' => $row_siswa->nisn,
                        'nis' => $row_siswa->nis,
                        'username' => $row_siswa->username,
                        'level' => 'siswa'
                    );
                    $this->session->set_userdata($sess_siswa);

                    // Redirect ke halaman siswa
                    redirect('history-pembayaran-siswa');
                } else {
                    $this->session->set_flashdata('login_gagal', 'Password Salah!');
                    redirect('login');
                }
            }
            // Jika tidak ada data
            else {
                $this->session->set_flashdata('login_gagal', 'Username tidak ditemukan!');
                redirect('login');
            }
        }
    }

}

/* End of file Login.php */
/* Location: ./application/controllers/Login.php */
