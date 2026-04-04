-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2026 at 07:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `poin_pelanggaran_siswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `kode_guru` char(8) NOT NULL,
  `nama_pengguna` varchar(100) DEFAULT NULL,
  `role` varchar(30) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(70) DEFAULT NULL,
  `aktif` enum('Y','N') DEFAULT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `telp` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`kode_guru`, `nama_pengguna`, `role`, `username`, `password`, `aktif`, `jabatan`, `telp`) VALUES
('0021.001', 'Drs. I Gusti Made Murjana, M.Pd.', 'Guru', 'Murjana', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Kepala Sekolah', '081805474228'),
('0021.002', 'I Nyoman Sucana, M.Kom.', 'Guru', 'sucana', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Waka Kurikulum', '08123650940'),
('0021.003', 'Bagus Putu Eka Wijaya, S.Kom.', 'Guru', 'guseka', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Waka Kesiswaan', '082146503026'),
('0021.004', 'Dewa Made Indra Suarmika, S.Kom.', 'Guru', 'indra', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Waka Sarana Prasarana', '082237442222'),
('0021.005', 'I Gede Pradipta Adi Nugraha, M.Kom.', 'Guru', 'dipta', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Waka Humas', '087861863842'),
('0021.006', 'I Gede Agung Abdi Prasetya, S.Ak.', 'Guru', 'abdi', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Komka AN', '082247033088'),
('0021.007', 'A.A Gede Putra Dwi Artajaya, S.Si., M.Kom.', 'Guru', 'artajaya', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Komka RPL', '082247033044'),
('0021.008', 'I Komang Arta Wijaya, M.Kom.', 'Guru', 'arta', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Komka DKV', '082247033030'),
('0021.009', 'I Made Sastrawan Adi Putra, S.Kom.', 'Guru', 'sastra', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Komka TKJ', '087837454455'),
('0021.010', 'Ni Wayan Sri Arini, ST., M.Kom.', 'Guru', 'sriarini', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081338512340'),
('0021.011', 'I Putu Urip Sutresna Mantra, S.Kom.', 'Guru', 'urip', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Ketua Lab', '087862656412'),
('0021.012', 'Ni Wayan Rumasni, S.Pd., M.Pd.', 'Guru', 'rum', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081338638999'),
('0021.013', 'I Wayan Agus Wiranata, S.Pd.', 'Guru', 'wiranata', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '082291355411'),
('0021.014', 'Nyoman Hendra Adi Wijaya, S.Pd., M.Kom.', 'Guru', 'hendra', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '085738216181'),
('0021.015', 'Dra. Ni Made Ayu Adnyani', 'Guru', 'ayuadnyani', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081238437877'),
('0021.016', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '083114537674'),
('0021.017', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '085738235218'),
('0021.018', 'Putu Yenny Suryantari, S.Pd.', 'Guru', 'yenny', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081353285810'),
('0021.019', 'I Gusti Ayu Sri Erna Dewi, SE.', 'Guru', 'erna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081220692219'),
('0021.020', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081338401856'),
('0021.021', 'Ida Ayu Indra Pratiwi, S.Sn.', 'Guru', 'dayuindra', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081239588346'),
('0021.022', 'I Wayan Sudarsa, S.Kom.', 'Guru', 'sudarsa', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081237896743'),
('0021.023', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '082247033088'),
('0021.024', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '082247033030'),
('0021.025', 'I Putu Dedy Karsana, S.Pd.', 'Guru', 'dedy', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '08563769773'),
('0021.026', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081238437877'),
('0021.027', 'I Putu Agus Sujana Adi Putra, S.Pd.', 'Guru', 'gussujana', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081237896743'),
('0021.028', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081933019479'),
('0021.029', 'Drs. I Gusti Putu Tirta Yasa, M.Pd.', 'Guru', 'tirta', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081338401856'),
('0021.030', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081220692219'),
('0021.031', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081353285810'),
('0021.032', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081338512340'),
('0021.033', 'Ni Putu Anita Prahandari, S.Pd.', 'Guru', 'anita', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '082146167817'),
('0021.034', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081338638999'),
('0021.035', 'I Kadek Yogi Mayudana, M.Pd.', 'Guru', 'yogi', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081933019479'),
('0021.036', 'Luh Putu Ayu Desiani, S.Kom., MM.', 'Guru', 'desi', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081805474228'),
('0021.037', 'I Kadek Puji Aksama, S.Pd.', 'Guru', 'aksama', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081933106676'),
('0021.038', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '082146503026'),
('0021.039', 'Ida Gusti Ayu Rinjani, M.Pd.', 'Guru', 'anjani', '$2y$10$L/11/fwOBwX3FJlyigeu.ehiLcZ1.cXX/ZugdZGP.fDSgHqWd25aK', 'Y', 'Guru BK XII', '081999976038'),
('0021.040', 'Ainul Mubsiroh, S.Pd.I, M.Pd.', 'Guru', 'ain', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '082247033044'),
('0021.041', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '087862656412'),
('0021.042', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '082237442222'),
('0021.043', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '085953912558'),
('0021.044', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '08563769773'),
('0021.045', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '087863112233'),
('0021.046', 'Masri Kagatanaribe, M.Pd.', 'Guru', 'masri', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '085739990443'),
('0021.047', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '087861863842'),
('0021.048', 'Luh Putu Trisma Prabawati, S.Kom.', 'Guru', 'trisma', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '083114537674'),
('0021.049', 'Kadek Diah Kertiana, S.Kom.', 'Guru', 'diah', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '082247033484'),
('0021.050', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '08123650940'),
('0021.051', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '083114537674'),
('0021.052', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '085738235218'),
('0021.053', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '082146167817'),
('0021.054', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081239588346'),
('0021.055', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081338401856'),
('0021.056', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '085738216181'),
('0021.057', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '083834969500'),
('0021.058', 'I Wayan Arik Sukariawan, S.Kom.', 'Guru', 'arik', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '082247033088'),
('0021.059', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '082247033030'),
('0021.060', 'I Putu Eka Mahendra, S.Kom.', 'Guru', 'eka', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '085739990443'),
('0021.061', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081238437877'),
('0021.062', 'Bella Cintya Devi, S.Kom.', 'Guru', 'bella', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081999022333'),
('0021.063', 'Darsusanto, S.Ag.', 'Guru', 'darsusanto', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081933019479'),
('0021.064', 'I Gusti Made Gunawan, S.Pd.', 'Guru', 'gun', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '087837454455'),
('0021.065', 'Ni Wayan Sriyaningsih, S.Sos.', 'Guru', 'anik', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081220692219'),
('0021.066', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081353285810'),
('0021.067', 'Nengah Dwi Rahayu, SE', 'Guru', 'dwirahayu', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081338512340'),
('0021.068', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081933106676'),
('0021.069', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081338638999'),
('0021.070', 'Ni Putu Tirta Purnama Dewi, S.Pd', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081999022333'),
('0021.071', 'Ni Nyoman Damayanti, S.Pd., M.Pd.', 'Guru', 'kotika', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081805474228'),
('0021.072', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '083117769593'),
('0021.073', 'Ni Wayan Lina Valentine, S.Pd.', 'Guru', 'lina', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '08970147321'),
('0021.074', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '081999976038'),
('0021.075', 'Nama_Pengguna', 'Guru', 'pengguna', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'N', 'Guru Mapel', '082247033044'),
('0021.076', 'Triono Doni Wijaya, S.Kom.', 'Guru', 'doni', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '087863112233'),
('0021.077', 'Nuri Sutiyaningsih, M.Kom.', 'Guru', 'nuri', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '082291355411'),
('0021.078', 'Kadek Arie Wira Kusuma, S.Kom.', 'Guru', 'ariewira', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '082247033484'),
('0021.079', 'Ni Putu Linda Agustini, S.Pd.', 'Guru', 'linda', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '085738235218'),
('0021.080', 'Ida Bagus Angga Baskara, S.Pd.', 'Guru', 'baskara', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '087863112233'),
('0021.081', 'Tjok Istri Agung Rai Sintha Devi, S.Pd.', 'Guru', 'sintha', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '0895347674833'),
('0021.082', 'Ida Bagus Maha Indra Prasada, S.Kom.', 'Guru', 'mahaindra', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '083117769593'),
('0021.083', 'Ida Ayu Dewi Paramita, S.Pd.', 'Guru', 'dayumita', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '0895375837712'),
('0021.084', 'Ni Luh Rosa Diarsanthi, S.Pd.', 'Guru', 'rosa', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '083834969500'),
('0021.085', 'Yustina Mariana Odang, S.Pd.', 'Guru', 'yustina', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '08123650940'),
('0021.086', 'Ida Ayu Amrita Pancajania, SE.', 'Guru', 'amrita', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '083114537674'),
('0021.087', 'A.A Gde Radika Mahaprasta Putra', 'Guru', 'radika', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '085738235218'),
('0021.088', 'Ni Ketut Supartini, SS.', 'Guru', 'tini', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '082146167817'),
('0021.089', 'M. Agus Wahyudi, S.Pd.', 'Guru', 'yudi', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081239588346'),
('0021.090', 'I Made Pranayama, S.Pd.', 'Guru', 'pranayama', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081338401856'),
('0021.091', 'Ni Kadek Chandra Putri Irani, S.Pd., M.Pd.', 'Guru', 'chandra', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '085953912558'),
('0021.092', 'I Dewa Ayu Setiyawati, S.Pd.', 'Guru', 'dayu', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '083834969500'),
('0021.093', 'Finsensius Ratuaki, M.Pd.', 'Guru', 'finsen', '$2y$10$L/11/fwOBwX3FJlyigeu.ehiLcZ1.cXX/ZugdZGP.fDSgHqWd25aK', 'Y', 'Guru BK X', '082247033088'),
('0021.094', 'Ni Putu Chintya Pradnya Suari, S.Pd.', 'Guru', 'chintya', '$2y$10$L/11/fwOBwX3FJlyigeu.ehiLcZ1.cXX/ZugdZGP.fDSgHqWd25aK', 'Y', 'Guru BK XI', '082247033030'),
('0021.095', 'Adventina Puspita', 'Guru', 'puspita', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '0895347674833'),
('0021.096', 'Aprianus Anjelius Foutnine, S.Fil', 'Guru', 'anjel', '$2y$10$qdT68nIqcmlV.AwvGFNX0eqMASuBMFMu7AJq0a.CAoNNQWT8qfxca', 'Y', 'Guru Mapel', '081238437877'),
('0021.097', 'Komang Niki Nugraha', 'Guru', 'sucana', '', 'Y', 'Guru Mapel', '089796958474'),
('0021.098', 'Okta Suryawan', 'Guru', 'okta', '$2y$10$q4sDY4nE.4KfNgbvaVQMe.8LknE1nRKXdG5SlMT02l5Zxc21W3rIG', 'Y', 'Guru BK XI', '0897960598'),
('0021.099', 'Yoga Arya Pratama', 'Guru', 'Yoga', '$2y$10$HFhTxHvALrE5xBTBwxRtFeui1qeUeGHtBn79UwI8oFBJRs8rXnZle', 'Y', 'Komka RPL', '089796958474');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_pelanggaran`
--

CREATE TABLE `jenis_pelanggaran` (
  `id_jenis_pelanggaran` int(5) NOT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `poin` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis_pelanggaran`
--

INSERT INTO `jenis_pelanggaran` (`id_jenis_pelanggaran`, `jenis`, `poin`) VALUES
(2, 'Kehadiran Di Sekolah', 5),
(3, 'Proses Belajar Mengajar', 6),
(4, 'Pelanggaran Norma Norma', 9),
(5, 'Pelanggaran Berat', 10),
(6, 'Kesopanan Berkendara', 8),
(7, 'Upacara Bendera', 4);

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id_kelas` int(5) NOT NULL,
  `id_tingkat` int(11) DEFAULT NULL,
  `id_program_keahlian` int(11) DEFAULT NULL,
  `rombel` int(11) DEFAULT NULL,
  `kode_guru` char(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id_kelas`, `id_tingkat`, `id_program_keahlian`, `rombel`, `kode_guru`) VALUES
(4, 3, 1, 4, '0021.039'),
(5, 3, 1, 5, '0021.007'),
(6, 3, 3, 1, '0021.011'),
(7, 3, 2, 1, '0021.004'),
(8, 3, 2, 2, '0021.091'),
(9, 3, 2, 3, '0021.025'),
(10, 3, 2, 4, '0021.076'),
(11, 3, 4, 1, '0021.060'),
(13, 2, 1, 2, '0021.077'),
(14, 2, 1, 3, '0021.078'),
(15, 2, 1, 4, '0021.002'),
(16, 2, 1, 5, '0021.048'),
(17, 2, 3, 1, '0021.079'),
(18, 2, 3, 2, '0021.033'),
(20, 2, 2, 2, '0021.029'),
(21, 2, 2, 3, '0021.014'),
(22, 2, 2, 4, '0021.084'),
(23, 2, 4, 1, '0021.006'),
(25, 1, 1, 2, '0021.081'),
(26, 1, 1, 3, '0021.015'),
(27, 1, 1, 4, '0021.022'),
(40, 3, 4, 2, '0021.008'),
(41, 1, 1, 1, '0021.008'),
(43, 2, 5, 1, '0021.019'),
(44, 3, 5, 1, '0021.019'),
(46, 3, 5, 5, '0021.098'),
(47, 1, 1, 1, '0021.001'),
(48, 2, 2, 2, '0021.012'),
(50, 1, 1, 3, '0021.016');

-- --------------------------------------------------------

--
-- Table structure for table `ortu_wali`
--

CREATE TABLE `ortu_wali` (
  `id_ortu_wali` int(5) NOT NULL,
  `ayah` varchar(50) DEFAULT NULL,
  `ibu` varchar(50) DEFAULT NULL,
  `wali` varchar(50) DEFAULT NULL,
  `tempat_lahir_ayah` varchar(20) NOT NULL,
  `tempat_lahir_ibu` varchar(20) NOT NULL,
  `tempat_lahir_wali` varchar(20) NOT NULL,
  `tanggal_lahir_ayah` date DEFAULT NULL,
  `tanggal_lahir_ibu` date DEFAULT NULL,
  `tanggal_lahir_wali` date DEFAULT NULL,
  `pekerjaan_ayah` varchar(50) DEFAULT NULL,
  `pekerjaan_ibu` varchar(50) DEFAULT NULL,
  `pekerjaan_wali` varchar(50) DEFAULT NULL,
  `no_telp_ayah` varchar(16) DEFAULT NULL,
  `no_telp_ibu` varchar(16) DEFAULT NULL,
  `no_telp_wali` varchar(16) DEFAULT NULL,
  `alamat_ayah` varchar(100) DEFAULT NULL,
  `alamat_ibu` varchar(100) DEFAULT NULL,
  `alamat_wali` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ortu_wali`
--

INSERT INTO `ortu_wali` (`id_ortu_wali`, `ayah`, `ibu`, `wali`, `tempat_lahir_ayah`, `tempat_lahir_ibu`, `tempat_lahir_wali`, `tanggal_lahir_ayah`, `tanggal_lahir_ibu`, `tanggal_lahir_wali`, `pekerjaan_ayah`, `pekerjaan_ibu`, `pekerjaan_wali`, `no_telp_ayah`, `no_telp_ibu`, `no_telp_wali`, `alamat_ayah`, `alamat_ibu`, `alamat_wali`) VALUES
(2, 'Lukman Halimah', 'Mega Rahma', NULL, 'Denpasar', '', '', '2026-04-04', NULL, NULL, 'Penjahit', 'Penjahit', NULL, '628621323300', '628621323300', NULL, 'Jalan Antasura, Perumahan Elit', 'Jalan Antasura, Perumahan Elit', NULL),
(3, 'Calvin Connor', 'Natalia Glover', NULL, '', '', '', '2026-04-04', NULL, NULL, 'Pengelola Properti', 'Pengelola Properti', NULL, '6282540268936', '628785988574', NULL, 'Jalan Sesetan, Gang No 5', 'Jalan Sesetan, Gang No 5', ''),
(20, 'Jak', 'Sri', '', '', '', '', '2026-04-04', NULL, NULL, 'Pelatih sepak bola', 'Menteri pribumi', '', '089706958484', '089706958484', '', 'JL Tukad Punggawa 22 panjer', 'JL Tukad Punggawa 22 Panjer', ''),
(22, '', '', 'Komang Adi', '', '', '', '2026-04-04', NULL, NULL, '', '', 'Buruh', '', '', '0897068548383', '', '', 'JL Waturenggong'),
(23, '', '', 'Komang Adi', '', '', '', '2026-04-04', NULL, NULL, '', '', 'Buruh', '', '', '0897068548383', '', '', 'JL Waturenggong'),
(27, '', '', '', '', '', '', '2026-04-04', NULL, NULL, '', '', '', '', '', '', '', '', ''),
(28, '', '', '', '', '', '', '2026-04-04', NULL, NULL, '', '', '', '', '', '', '', '', ''),
(29, 'WQWWEWE', 'QWEWQWEE', '', '', '', '', '2026-04-04', NULL, NULL, 'ASDSDA', 'QDWWQQWD', '', '08970594003', '08786950904', '', 'SDADADADS', 'WDQSDWQDW', ''),
(30, 'WQWWEWE', 'QWEWQWEE', '', '', '', '', '2026-04-04', NULL, NULL, 'ASDSDA', 'QDWWQQWD', '', '08970594003', '08786950904', '', 'SDADADADS', 'WDQSDWQDW', ''),
(31, 'Suhandi', '', '', '', '', '', '2026-04-04', NULL, NULL, 'Pelatih Sepak Bola', '', '', '089685948383', '', '', 'JL Melaya Desa Adat Penarungan', '', ''),
(36, 'Suhandi Junior', '', '', '', '', '', '2026-04-04', NULL, NULL, 'Pilot', '', '', '08970594233', '', '', 'JL Pulet ', '', ''),
(37, 'Bambang Saptajia', '', '', '', '', '', '2026-04-04', NULL, NULL, 'Programmer', '', '', '08970695855', '', '', 'JL Tukad Nyali', '', ''),
(38, 'Wayan Laksana', 'Kadek Siti Adnyani', '', '', '', '', '2026-04-04', NULL, NULL, 'Sopir', 'Ibu Rumah Tanggga', '', '0897069584352', '089706958412', '', 'JL. Tukad Unda', 'JL. Tukad Unda', '');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggaran_siswa`
--

CREATE TABLE `pelanggaran_siswa` (
  `id_pelanggaran_siswa` int(5) NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `nis` int(5) DEFAULT NULL,
  `id_jenis_pelanggaran` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggaran_siswa`
--

INSERT INTO `pelanggaran_siswa` (`id_pelanggaran_siswa`, `tanggal`, `nis`, `id_jenis_pelanggaran`, `keterangan`) VALUES
(1, '2026-01-02 09:26:32', 9124, 2, 'Terlambat masuk hari Senin'),
(2, '2026-01-02 09:26:35', 9125, 3, 'Tidak mengerjakan PR'),
(4, '2026-03-31 09:14:05', 9124, 3, 'Ribut dikelas'),
(5, '2026-04-01 13:23:53', 9124, 5, 'Berbicara Kasar kepada kepala sekolah'),
(6, '2026-04-01 14:50:45', 9124, 5, 'Melakukan penganiayaan terhadapat kepala sekolah\r\n'),
(7, '2026-04-01 14:51:03', 9124, 5, 'Membawa sabu di sekolah'),
(8, '2026-04-01 14:51:19', 9124, 5, 'BASJC BASC BC  '),
(9, '2026-04-02 12:05:57', 9124, 2, 'sbbhbhdjq'),
(10, '2026-04-02 15:31:44', 9125, 5, 'qwe'),
(11, '2026-04-02 15:32:04', 9125, 5, 'gfhgfg'),
(12, '2026-04-02 15:32:17', 9125, 5, 'vvjhvvjh'),
(13, '2026-04-03 01:58:30', 9125, 5, 'Smackdown Kepala Sekolah'),
(14, '2026-04-03 01:58:46', 9125, 5, 'Membuka brangkas sekolah'),
(15, '2026-04-03 02:13:47', 9126, 5, 'Memukul Guru '),
(16, '2026-04-03 02:14:03', 9126, 5, 'Mengatakan Guru Goblok'),
(17, '2026-04-03 02:14:22', 9126, 5, 'Melakukan Pemerkosaan'),
(18, '2026-04-03 02:14:35', 9126, 3, 'Bermain HP'),
(19, '2026-04-03 02:15:01', 9126, 4, 'Berkata Kasar'),
(20, '2026-04-03 02:15:42', 9126, 5, 'Mengambilo uang teman'),
(21, '2026-04-03 02:18:56', 9126, 7, 'Tidak membawa topi'),
(22, '2026-04-03 07:17:22', 9127, 7, 'Tidak membawa topi\r\n'),
(23, '2026-04-03 08:36:03', 9127, 5, 'bahdbsahdbaj'),
(24, '2026-04-03 08:36:12', 9127, 5, 'bdsbjbj'),
(25, '2026-04-03 08:36:28', 9127, 4, 'sdb qj'),
(26, '2026-04-03 15:56:40', 9128, 5, 'Mengambil barang teman'),
(27, '2026-04-03 15:56:58', 9128, 5, 'akakakakak'),
(28, '2026-04-03 15:57:06', 9128, 5, 'anajsnaajsa'),
(29, '2026-04-03 15:57:23', 9128, 5, 'sndbbsqkjqbk'),
(30, '2026-04-03 15:57:30', 9128, 5, 'skdnaksninsiqn'),
(31, '2026-04-03 15:57:38', 9128, 5, 'sbdaja');

-- --------------------------------------------------------

--
-- Table structure for table `perjanjian_orang_tua`
--

CREATE TABLE `perjanjian_orang_tua` (
  `id_perjanjian_ortu` int(5) NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `id_pelanggaran_siswa` int(5) DEFAULT NULL,
  `status` enum('Masih Proses','Selesai') DEFAULT NULL,
  `foto_dokumen` varchar(100) DEFAULT NULL,
  `tingkat` varchar(3) DEFAULT NULL,
  `nama_ortu` varchar(100) NOT NULL,
  `pekerjaan_ortu` varchar(50) NOT NULL,
  `alamat_ortu` varchar(100) NOT NULL,
  `no_telp_ortu` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perjanjian_orang_tua`
--

INSERT INTO `perjanjian_orang_tua` (`id_perjanjian_ortu`, `tanggal`, `id_pelanggaran_siswa`, `status`, `foto_dokumen`, `tingkat`, `nama_ortu`, `pekerjaan_ortu`, `alamat_ortu`, `no_telp_ortu`) VALUES
(3, '2026-04-04 12:27:29', 10, 'Masih Proses', NULL, 'XII', 'Lukman Halimah', 'Penjahit', 'Jalan Antasura, Perumahan Elit', '628621323300', 0),
(4, '2026-04-04 12:27:29', 11, 'Masih Proses', NULL, 'XII', 'Lukman Halimah', 'Penjahit', 'Jalan Antasura, Perumahan Elit', '628621323300', 0),
(5, '2026-04-04 12:27:29', 12, 'Masih Proses', NULL, 'XII', 'Lukman Halimah', 'Penjahit', 'Jalan Antasura, Perumahan Elit', '628621323300', 0),
(6, '2026-04-04 12:27:29', 13, 'Masih Proses', NULL, 'XII', 'Lukman Halimah', 'Penjahit', 'Jalan Antasura, Perumahan Elit', '628621323300', 0),
(7, '2026-04-04 12:27:29', 14, 'Masih Proses', NULL, 'XII', 'Lukman Halimah', 'Penjahit', 'Jalan Antasura, Perumahan Elit', '628621323300', 0);

-- --------------------------------------------------------

--
-- Table structure for table `perjanjian_siswa`
--

CREATE TABLE `perjanjian_siswa` (
  `id_perjanjian_siswa` int(5) NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `id_pelanggaran_siswa` int(11) DEFAULT NULL,
  `status` enum('Masih Proses','Selesai') DEFAULT NULL,
  `foto_dokumen` varchar(100) DEFAULT NULL,
  `tingkat` varchar(3) DEFAULT NULL,
  `nama_ortu` varchar(100) NOT NULL,
  `pekerjaan_ortu` varchar(50) NOT NULL,
  `alamat_ortu` varchar(100) NOT NULL,
  `no_telp_ortu` varchar(16) NOT NULL,
  `wali_kelas` varchar(100) NOT NULL,
  `guru_bk` varchar(100) NOT NULL,
  `wakasek_kesiswaan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perjanjian_siswa`
--

INSERT INTO `perjanjian_siswa` (`id_perjanjian_siswa`, `tanggal`, `id_pelanggaran_siswa`, `status`, `foto_dokumen`, `tingkat`, `nama_ortu`, `pekerjaan_ortu`, `alamat_ortu`, `no_telp_ortu`, `wali_kelas`, `guru_bk`, `wakasek_kesiswaan`) VALUES
(1, '2026-01-02 09:26:31', 1, 'Masih Proses', NULL, 'X', '', '', '', '', '', '', ''),
(4, '2026-04-03 16:07:20', 22, 'Selesai', 'Aplikasi Poin Pelanggaran Siswa_page-0001.jpg', 'X', 'Bambang Saptaji', 'Programmer', 'JL Tukad Nyali', '08970695855', 'Tjok Istri Agung Rai Sintha Devi, S.Pd.', 'Finsensius Ratuaki, M.Pd.', 'Bagus Putu Eka Wijaya, S.Kom.'),
(5, '2026-04-03 16:07:20', 23, 'Selesai', 'Aplikasi Poin Pelanggaran Siswa_page-0001.jpg', 'X', 'Bambang Saptaji', 'Programmer', 'JL Tukad Nyali', '08970695855', 'Tjok Istri Agung Rai Sintha Devi, S.Pd.', 'Finsensius Ratuaki, M.Pd.', 'Bagus Putu Eka Wijaya, S.Kom.'),
(6, '2026-04-03 16:07:20', 24, 'Selesai', 'Aplikasi Poin Pelanggaran Siswa_page-0001.jpg', 'X', 'Bambang Saptaji', 'Programmer', 'JL Tukad Nyali', '08970695855', 'Tjok Istri Agung Rai Sintha Devi, S.Pd.', 'Finsensius Ratuaki, M.Pd.', 'Bagus Putu Eka Wijaya, S.Kom.'),
(7, '2026-04-03 16:07:20', 25, 'Selesai', 'Aplikasi Poin Pelanggaran Siswa_page-0001.jpg', 'X', 'Bambang Saptaji', 'Programmer', 'JL Tukad Nyali', '08970695855', 'Tjok Istri Agung Rai Sintha Devi, S.Pd.', 'Finsensius Ratuaki, M.Pd.', 'Bagus Putu Eka Wijaya, S.Kom.'),
(8, '2026-04-03 16:38:18', 15, 'Masih Proses', NULL, 'XI', 'Suhandi Junior', 'Pilot', 'JL Pulet ', '08970594233', 'Luh Putu Trisma Prabawati, S.Kom.', 'Ni Putu Chintya Pradnya Suari, S.Pd.', 'Bagus Putu Eka Wijaya, S.Kom.'),
(9, '2026-04-03 16:38:18', 16, 'Masih Proses', NULL, 'XI', 'Suhandi Junior', 'Pilot', 'JL Pulet ', '08970594233', 'Luh Putu Trisma Prabawati, S.Kom.', 'Ni Putu Chintya Pradnya Suari, S.Pd.', 'Bagus Putu Eka Wijaya, S.Kom.'),
(10, '2026-04-03 16:38:18', 17, 'Masih Proses', NULL, 'XI', 'Suhandi Junior', 'Pilot', 'JL Pulet ', '08970594233', 'Luh Putu Trisma Prabawati, S.Kom.', 'Ni Putu Chintya Pradnya Suari, S.Pd.', 'Bagus Putu Eka Wijaya, S.Kom.'),
(11, '2026-04-03 16:38:18', 18, 'Masih Proses', NULL, 'XI', 'Suhandi Junior', 'Pilot', 'JL Pulet ', '08970594233', 'Luh Putu Trisma Prabawati, S.Kom.', 'Ni Putu Chintya Pradnya Suari, S.Pd.', 'Bagus Putu Eka Wijaya, S.Kom.'),
(12, '2026-04-03 16:38:18', 19, 'Masih Proses', NULL, 'XI', 'Suhandi Junior', 'Pilot', 'JL Pulet ', '08970594233', 'Luh Putu Trisma Prabawati, S.Kom.', 'Ni Putu Chintya Pradnya Suari, S.Pd.', 'Bagus Putu Eka Wijaya, S.Kom.'),
(13, '2026-04-03 16:38:18', 20, 'Masih Proses', NULL, 'XI', 'Suhandi Junior', 'Pilot', 'JL Pulet ', '08970594233', 'Luh Putu Trisma Prabawati, S.Kom.', 'Ni Putu Chintya Pradnya Suari, S.Pd.', 'Bagus Putu Eka Wijaya, S.Kom.'),
(14, '2026-04-03 16:38:18', 21, 'Masih Proses', NULL, 'XI', 'Suhandi Junior', 'Pilot', 'JL Pulet ', '08970594233', 'Luh Putu Trisma Prabawati, S.Kom.', 'Ni Putu Chintya Pradnya Suari, S.Pd.', 'Bagus Putu Eka Wijaya, S.Kom.');

-- --------------------------------------------------------

--
-- Table structure for table `profil_sekolah`
--

CREATE TABLE `profil_sekolah` (
  `id_profil_sekolah` int(5) NOT NULL,
  `nama_sekolah` varchar(50) DEFAULT NULL,
  `alamat_sekolah` varchar(100) DEFAULT NULL,
  `kota` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profil_sekolah`
--

INSERT INTO `profil_sekolah` (`id_profil_sekolah`, `nama_sekolah`, `alamat_sekolah`, `kota`) VALUES
(1, 'SMKS TI Bali Global Denpasar', 'Kecamatan Denpasar Selatan, Kota Denpasar, Provinsi Bali', 'Denpasar');

-- --------------------------------------------------------

--
-- Table structure for table `program_keahlian`
--

CREATE TABLE `program_keahlian` (
  `id_program_keahlian` int(5) NOT NULL,
  `program_keahlian` varchar(6) DEFAULT NULL,
  `deskripsi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_keahlian`
--

INSERT INTO `program_keahlian` (`id_program_keahlian`, `program_keahlian`, `deskripsi`) VALUES
(1, 'RPL', 'Rekayasa Perangkat Lunak'),
(2, 'DKV', 'Desain Komunikasi Visual'),
(3, 'TKJ', 'Teknik Komputer Jaringan'),
(4, 'AN', 'Animasi'),
(5, 'BD', 'Bisnis Digital');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `nis` int(5) NOT NULL,
  `nama_siswa` varchar(50) DEFAULT NULL,
  `jenis_kelamin` enum('Laki - Laki','Perempuan') DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `password` varchar(70) DEFAULT NULL,
  `status` enum('aktif','tidak_aktif','lulus','pindah') DEFAULT NULL,
  `id_ortu_wali` int(5) DEFAULT NULL,
  `id_kelas` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`nis`, `nama_siswa`, `jenis_kelamin`, `alamat`, `password`, `status`, `id_ortu_wali`, `id_kelas`) VALUES
(9124, 'Ryan', 'Laki - Laki', 'Psr Jajtinegara Bl BKS/30, Dki Jakarta', '$2y$10$.V6ZwUrLKYZ1hOOLJxylxOsblZuqOiPaHbZvi9aEWlVmRyl.aXgwG', '', 3, 4),
(9125, 'Narin', 'Perempuan', 'Jl Janur 11 43 RT 003/07 Pd Kelapa, Dki Jakarta', '$2y$10$.V6ZwUrLKYZ1hOOLJxylxOsblZuqOiPaHbZvi9aEWlVmRyl.aXgwG', 'aktif', 2, 5),
(9126, 'Reza Rahardian', 'Laki - Laki', 'JL Pulet ', '$2y$10$WM001wl06K51hn8p556ec.7kMy7iGFcku83v8WWCgo98AAtfo.cP6', 'aktif', 36, 16),
(9127, 'Abel', 'Laki - Laki', 'JL Tukad Nyali', '$2y$10$tk8dnLS.8o8z4JFyvQzGx.zYMQGs629Lxh3N2wA5sgZLvtC4Ku98S', 'aktif', 37, 25),
(9128, 'Komang Niki Nugraha', 'Laki - Laki', 'JL. Tukad Unda', '$2y$10$0uIq43MwDZy.TXnxG5BpHOzF5veaJyg24p7fNFa5.DD37cUomIBnm', 'aktif', 38, 13);

-- --------------------------------------------------------

--
-- Table structure for table `surat_keluar`
--

CREATE TABLE `surat_keluar` (
  `id_surat_keluar` int(5) NOT NULL,
  `no_surat` varchar(30) DEFAULT NULL,
  `jenis_surat` varchar(30) DEFAULT NULL,
  `id_surat_pindah` int(5) DEFAULT NULL,
  `nis` int(5) DEFAULT NULL,
  `tanggal_pembuatan_surat` date DEFAULT NULL,
  `id_profil_sekolah` int(2) DEFAULT NULL,
  `id_tahun_ajaran` int(3) DEFAULT NULL,
  `tingkat` varchar(3) DEFAULT NULL,
  `tanggal_pemanggilan` datetime NOT NULL,
  `keperluan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_keluar`
--

INSERT INTO `surat_keluar` (`id_surat_keluar`, `no_surat`, `jenis_surat`, `id_surat_pindah`, `nis`, `tanggal_pembuatan_surat`, `id_profil_sekolah`, `id_tahun_ajaran`, `tingkat`, `tanggal_pemanggilan`, `keperluan`) VALUES
(21, '1/SMK TI/BG/IV/2026', 'Panggilan Orang Tua', NULL, 9124, '2026-04-03', 1, 5, 'XII', '2026-04-04 12:00:00', 'Tindak lanjut masalah bullying');

-- --------------------------------------------------------

--
-- Table structure for table `surat_pindah`
--

CREATE TABLE `surat_pindah` (
  `id_surat_pindah` int(5) NOT NULL,
  `sekolah_tujuan` varchar(100) DEFAULT NULL,
  `alasan_pindah` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_pindah`
--

INSERT INTO `surat_pindah` (`id_surat_pindah`, `sekolah_tujuan`, `alasan_pindah`) VALUES
(1, 'SMAN 1 Surakarta', 'Mengikuti perpindahan dinas orang tua'),
(2, 'SMKS Harapan', 'Mengikuti perpindahan dinas orang tua'),
(3, 'SMKN 2 Denpasar', 'Mengikuti perpindahan dinas orang tua');

-- --------------------------------------------------------

--
-- Table structure for table `tahun_ajaran`
--

CREATE TABLE `tahun_ajaran` (
  `id_tahun_ajaran` int(3) NOT NULL,
  `tahun` varchar(10) DEFAULT NULL,
  `aktif` enum('Y','N') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tahun_ajaran`
--

INSERT INTO `tahun_ajaran` (`id_tahun_ajaran`, `tahun`, `aktif`) VALUES
(1, '2021/2022', 'N'),
(2, '2022/2023', 'N'),
(3, '2023/2024', 'N'),
(4, '2024/2025', 'N'),
(5, '2025/2026', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `tingkat`
--

CREATE TABLE `tingkat` (
  `id_tingkat` int(1) NOT NULL,
  `tingkat` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tingkat`
--

INSERT INTO `tingkat` (`id_tingkat`, `tingkat`) VALUES
(1, 'X'),
(2, 'XI'),
(3, 'XII');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`kode_guru`);

--
-- Indexes for table `jenis_pelanggaran`
--
ALTER TABLE `jenis_pelanggaran`
  ADD PRIMARY KEY (`id_jenis_pelanggaran`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id_kelas`),
  ADD KEY `id_tingkat` (`id_tingkat`),
  ADD KEY `id_program_keahlian` (`id_program_keahlian`),
  ADD KEY `kode_guru` (`kode_guru`);

--
-- Indexes for table `ortu_wali`
--
ALTER TABLE `ortu_wali`
  ADD PRIMARY KEY (`id_ortu_wali`);

--
-- Indexes for table `pelanggaran_siswa`
--
ALTER TABLE `pelanggaran_siswa`
  ADD PRIMARY KEY (`id_pelanggaran_siswa`),
  ADD KEY `nis` (`nis`),
  ADD KEY `id_jenis_pelanggaran` (`id_jenis_pelanggaran`);

--
-- Indexes for table `perjanjian_orang_tua`
--
ALTER TABLE `perjanjian_orang_tua`
  ADD PRIMARY KEY (`id_perjanjian_ortu`),
  ADD KEY `id_pelanggaran_siswa` (`id_pelanggaran_siswa`);

--
-- Indexes for table `perjanjian_siswa`
--
ALTER TABLE `perjanjian_siswa`
  ADD PRIMARY KEY (`id_perjanjian_siswa`),
  ADD KEY `id_pelanggaran_siswa` (`id_pelanggaran_siswa`);

--
-- Indexes for table `profil_sekolah`
--
ALTER TABLE `profil_sekolah`
  ADD PRIMARY KEY (`id_profil_sekolah`);

--
-- Indexes for table `program_keahlian`
--
ALTER TABLE `program_keahlian`
  ADD PRIMARY KEY (`id_program_keahlian`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`nis`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `siswa_ibfk_1` (`id_ortu_wali`);

--
-- Indexes for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD PRIMARY KEY (`id_surat_keluar`),
  ADD KEY `id_pindah_sekolah` (`id_surat_pindah`),
  ADD KEY `nis` (`nis`),
  ADD KEY `id_profil_sekolah` (`id_profil_sekolah`),
  ADD KEY `id_tahun_ajaran` (`id_tahun_ajaran`);

--
-- Indexes for table `surat_pindah`
--
ALTER TABLE `surat_pindah`
  ADD PRIMARY KEY (`id_surat_pindah`);

--
-- Indexes for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  ADD PRIMARY KEY (`id_tahun_ajaran`);

--
-- Indexes for table `tingkat`
--
ALTER TABLE `tingkat`
  ADD PRIMARY KEY (`id_tingkat`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jenis_pelanggaran`
--
ALTER TABLE `jenis_pelanggaran`
  MODIFY `id_jenis_pelanggaran` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id_kelas` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `ortu_wali`
--
ALTER TABLE `ortu_wali`
  MODIFY `id_ortu_wali` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `pelanggaran_siswa`
--
ALTER TABLE `pelanggaran_siswa`
  MODIFY `id_pelanggaran_siswa` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `perjanjian_orang_tua`
--
ALTER TABLE `perjanjian_orang_tua`
  MODIFY `id_perjanjian_ortu` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `perjanjian_siswa`
--
ALTER TABLE `perjanjian_siswa`
  MODIFY `id_perjanjian_siswa` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `profil_sekolah`
--
ALTER TABLE `profil_sekolah`
  MODIFY `id_profil_sekolah` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `program_keahlian`
--
ALTER TABLE `program_keahlian`
  MODIFY `id_program_keahlian` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  MODIFY `id_surat_keluar` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `surat_pindah`
--
ALTER TABLE `surat_pindah`
  MODIFY `id_surat_pindah` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  MODIFY `id_tahun_ajaran` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tingkat`
--
ALTER TABLE `tingkat`
  MODIFY `id_tingkat` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`id_tingkat`) REFERENCES `tingkat` (`id_tingkat`) ON DELETE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_2` FOREIGN KEY (`id_program_keahlian`) REFERENCES `program_keahlian` (`id_program_keahlian`),
  ADD CONSTRAINT `kelas_ibfk_3` FOREIGN KEY (`kode_guru`) REFERENCES `guru` (`kode_guru`);

--
-- Constraints for table `pelanggaran_siswa`
--
ALTER TABLE `pelanggaran_siswa`
  ADD CONSTRAINT `pelanggaran_siswa_ibfk_1` FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`),
  ADD CONSTRAINT `pelanggaran_siswa_ibfk_2` FOREIGN KEY (`id_jenis_pelanggaran`) REFERENCES `jenis_pelanggaran` (`id_jenis_pelanggaran`);

--
-- Constraints for table `perjanjian_orang_tua`
--
ALTER TABLE `perjanjian_orang_tua`
  ADD CONSTRAINT `perjanjian_orang_tua_ibfk_1` FOREIGN KEY (`id_pelanggaran_siswa`) REFERENCES `pelanggaran_siswa` (`id_pelanggaran_siswa`);

--
-- Constraints for table `perjanjian_siswa`
--
ALTER TABLE `perjanjian_siswa`
  ADD CONSTRAINT `perjanjian_siswa_ibfk_1` FOREIGN KEY (`id_pelanggaran_siswa`) REFERENCES `pelanggaran_siswa` (`id_pelanggaran_siswa`);

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`id_ortu_wali`) REFERENCES `ortu_wali` (`id_ortu_wali`),
  ADD CONSTRAINT `siswa_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE;

--
-- Constraints for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD CONSTRAINT `surat_keluar_ibfk_2` FOREIGN KEY (`id_surat_pindah`) REFERENCES `surat_pindah` (`id_surat_pindah`),
  ADD CONSTRAINT `surat_keluar_ibfk_3` FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`),
  ADD CONSTRAINT `surat_keluar_ibfk_4` FOREIGN KEY (`id_profil_sekolah`) REFERENCES `profil_sekolah` (`id_profil_sekolah`),
  ADD CONSTRAINT `surat_keluar_ibfk_5` FOREIGN KEY (`id_tahun_ajaran`) REFERENCES `tahun_ajaran` (`id_tahun_ajaran`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
