<?php
namespace QLPhongTro\Models;

class PhongTro {
    private $conn;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
    }

    // Lấy tất cả phòng trọ (kèm tìm kiếm + phân trang)
    public function getAll($search = '', $limit = 10, $offset = 0) {
        $sql = "SELECT * FROM PhongTro WHERE 1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (tieuDe LIKE :kw OR moTa LIKE :kw OR diaChiCuThe LIKE :kw)";
            $params[':kw'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Đếm tổng phòng trọ cho phân trang
    public function count($search = '') {
        $sql = "SELECT COUNT(*) as total FROM PhongTro WHERE 1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (tieuDe LIKE :kw OR moTa LIKE :kw OR diaChiCuThe LIKE :kw)";
            $params[':kw'] = '%' . $search . '%';
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    // Lấy tất cả phòng trọ (không phân trang)
    public function all() {
        $stmt = $this->conn->query("SELECT * FROM PhongTro ORDER BY id DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy phòng trọ theo ID
    public function find($id) {
        $stmt = $this->conn->prepare("SELECT * FROM PhongTro WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Thêm phòng trọ mới
    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO PhongTro (tieuDe, diaChiCuThe, diaDiem_id, gia, dienTich, moTa, trangThai, nguoiDang_id)
            VALUES (:tieuDe, :diaChiCuThe, :diaDiem_id, :gia, :dienTich, :moTa, :trangThai, :nguoiDang_id)
        ");
        $stmt->execute($data);
        return $this->conn->lastInsertId();
    }

    // Cập nhật phòng trọ
    public function update($id, $data) {
        $data[':id'] = $id;
        $stmt = $this->conn->prepare("
            UPDATE PhongTro 
            SET tieuDe = :tieuDe, diaChiCuThe = :diaChiCuThe, diaDiem_id = :diaDiem_id,
                gia = :gia, dienTich = :dienTich, moTa = :moTa, trangThai = :trangThai, nguoiDang_id = :nguoiDang_id
            WHERE id = :id
        ");
        return $stmt->execute($data);
    }

    // Xoá phòng trọ
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM PhongTro WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Lấy phòng trọ theo người đăng
    public function getByNguoiDang($nguoiDang_id) {
        $stmt = $this->conn->prepare("SELECT * FROM PhongTro WHERE nguoiDang_id = :nguoiDang_id");
        $stmt->execute([':nguoiDang_id' => $nguoiDang_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Tìm kiếm phòng trọ (full)
    public function search($keyword) {
        $like = '%' . $keyword . '%';
        $stmt = $this->conn->prepare("
            SELECT * FROM PhongTro 
            WHERE tieuDe LIKE :kw OR moTa LIKE :kw OR diaChiCuThe LIKE :kw
        ");
        $stmt->execute([':kw' => $like]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
