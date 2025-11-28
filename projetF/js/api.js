/**
 * Client API pour communiquer avec le backend PHP
 */

const API = {
    baseURL: '/projetF/php',
    
    /**
     * Récupérer tous les étudiants
     */
    async getStudents() {
        try {
            const response = await fetch(`${this.baseURL}/get_students.php`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Erreur getStudents:', error);
            return { success: false, message: error.message };
        }
    },
    
    /**
     * Ajouter un étudiant
     */
    async addStudent(studentData) {
        try {
            const response = await fetch(`${this.baseURL}/add_student.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(studentData)
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Erreur addStudent:', error);
            return { success: false, message: error.message };
        }
    },
    
    /**
     * Sauvegarder les présences
     */
    async saveAttendance(attendanceData) {
        try {
            const response = await fetch(`${this.baseURL}/save_attendance.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ attendance: attendanceData })
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Erreur saveAttendance:', error);
            return { success: false, message: error.message };
        }
    }
};

console.log('✅ API Client chargé');