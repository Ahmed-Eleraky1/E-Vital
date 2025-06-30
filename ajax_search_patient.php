<?php
include 'db.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
    exit('غير مصرح');
}

$doctor_id = $_SESSION['doctor_id'];
$search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';

$sql = "SELECT DISTINCT p.* 
        FROM patients p 
        JOIN patient_doctors pd ON p.patient_id = pd.patient_id 
        WHERE pd.doctor_id = ? AND 
        (p.name LIKE ? OR 
         p.contact_number LIKE ? OR 
         p.email LIKE ?)";

$stmt = $conn->prepare($sql);
$search_param = "%" . $search_query . "%";
$stmt->bind_param("isss", $doctor_id, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

while ($patient = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($patient['name']); ?></td>
        <td><?php echo htmlspecialchars($patient['date_of_birth']); ?></td>
        <td><?php echo htmlspecialchars($patient['gender']); ?></td>
        <td><?php echo htmlspecialchars($patient['email']); ?></td>
        <td>
            <a href="view_patient.php?patient_id=<?php echo $patient['patient_id']; ?>" class="btn btn-info">عرض</a>
        </td>
    </tr>
<?php endwhile; ?>
