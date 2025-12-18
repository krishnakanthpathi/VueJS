<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vue.js PHP CRUD with Hierarchy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .tree-view {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            max-height: 500px;
            overflow-y: auto;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div id="app" class="container mt-4">
    <h2 class="mb-4 text-center">Organization Hierarchy CRUD</h2>

    <div class="row">
        <div class="col-md-7">
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    {{ isEditing ? 'Edit Employee' : 'Add New Employee' }}
                </div>
                <div class="card-body">
                    <form @submit.prevent="saveEmployee">
                        <div class="mb-2">
                            <label>Name:</label>
                            <input v-model="form.name" type="text" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Designation:</label>
                            <input v-model="form.designation" type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Manager ID (Optional):</label>
                            <select v-model="form.manager_id" class="form-control">
                                <option :value="null">None (Top Level)</option>
                                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                                    {{ emp.id }} - {{ emp.name }}
                                </option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">{{ isEditing ? 'Update' : 'Save' }}</button>
                        <button type="button" v-if="isEditing" @click="resetForm" class="btn btn-secondary">Cancel</button>
                    </form>
                </div>
            </div>

            <h4>Employee List (Flat Data)</h4>
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Manager ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="emp in employees" :key="emp.id">
                        <td>{{ emp.id }}</td>
                        <td>{{ emp.name }}</td>
                        <td>{{ emp.designation }}</td>
                        <td>{{ emp.manager_id || 'NULL' }}</td>
                        <td>
                            <button @click="editEmployee(emp)" class="btn btn-sm btn-warning">Edit</button>
                            <button @click="deleteEmployee(emp.id)" class="btn btn-sm btn-danger ms-1">Del</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-md-5">
            <h4>JSON Tree Hierarchy</h4>
            <p class="text-muted small">Generated automatically from the flat list.</p>
            <pre class="tree-view">{{ hierarchyTree }}</pre>
        </div>
    </div>
</div>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                employees: [],
                form: {
                    id: null,
                    name: '',
                    designation: '',
                    manager_id: null
                },
                isEditing: false
            };
        },
        computed: {
            // Logic to convert flat list to tree hierarchy
            hierarchyTree() {
                // 1. Create a deep copy to avoid modifying original data during sort/map
                let data = JSON.parse(JSON.stringify(this.employees));
                
                let map = {};
                let node;
                let roots = [];
                let i;
                
                // 2. Initialize map and add children array to objects
                for (i = 0; i < data.length; i += 1) {
                    map[data[i].id] = i; 
                    data[i].children = []; 
                }
                
                // 3. Link children to parents
                for (i = 0; i < data.length; i += 1) {
                    node = data[i];
                    if (node.manager_id !== null && map[node.manager_id] !== undefined) {
                        data[map[node.manager_id]].children.push(node);
                    } else {
                        roots.push(node);
                    }
                }
                
                // Return formatted JSON string
                return JSON.stringify(roots, null, 2);
            }
        },
        methods: {
            // GET Employees
            fetchEmployees() {
                axios.get('dynamictree.php')
                    .then(response => {
                        this.employees = response.data;
                    })
                    .catch(error => console.error(error));
            },
            // Create or Update
            saveEmployee() {
                if (this.isEditing) {
                    axios.put('dynamictree.php', this.form)
                        .then(() => {
                            this.fetchEmployees();
                            this.resetForm();
                        });
                } else {
                    axios.post('dynamictree.php', this.form)
                        .then(() => {
                            this.fetchEmployees();
                            this.resetForm();
                        });
                }
            },
            // Prepare Edit
            editEmployee(emp) {
                this.form = { ...emp }; // Clone object
                this.isEditing = true;
            },
            // Delete
            deleteEmployee(id) {
                if(confirm("Are you sure?")) {
                    axios.delete(`dynamictree.php?id=${id}`)
                        .then(() => {
                            this.fetchEmployees();
                        });
                }
            },
            resetForm() {
                this.form = { id: null, name: '', designation: '', manager_id: null };
                this.isEditing = false;
            }
        },
        mounted() {
            this.fetchEmployees();
        }
    }).mount('#app');
</script>

</body>
</html>


<?php
// dynamictree.php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: Content-Type");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
// header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$db_name = "company_db";
$username = "root"; // Update with your DB username
$password = "";     // Update with your DB password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(["message" => "Connection failed: " . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        // Read: Fetch all employees
        $stmt = $conn->prepare("SELECT * FROM employees ORDER BY id ASC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        // Create: Add new employee
        if(isset($input['name']) && isset($input['designation'])) {
            $manager_id = !empty($input['manager_id']) ? $input['manager_id'] : NULL;
            $sql = "INSERT INTO employees (name, designation, manager_id) VALUES (:name, :desig, :mgr)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':name' => $input['name'], ':desig' => $input['designation'], ':mgr' => $manager_id]);
            echo json_encode(["message" => "Employee Created"]);
        }
        break;

    case 'PUT':
        // Update: Edit existing employee
        if(isset($input['id']) && isset($input['name'])) {
            $manager_id = !empty($input['manager_id']) ? $input['manager_id'] : NULL;
            $sql = "UPDATE employees SET name = :name, designation = :desig, manager_id = :mgr WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':name' => $input['name'], ':desig' => $input['designation'], ':mgr' => $manager_id, ':id' => $input['id']]);
            echo json_encode(["message" => "Employee Updated"]);
        }
        break;

    case 'DELETE':
        // Delete: Remove employee
        if(isset($_GET['id'])) {
            $stmt = $conn->prepare("DELETE FROM employees WHERE id = :id");
            $stmt->execute([':id' => $_GET['id']]);
            echo json_encode(["message" => "Employee Deleted"]);
        }
        break;
}
?>