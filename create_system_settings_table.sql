CREATE TABLE IF NOT EXISTS system_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO system_settings (setting_key, value, description) VALUES
('system_name', 'نظام إدارة الأمراض المزمنة', 'اسم النظام'),
('max_appointments', '10', 'الحد الأقصى للمواعيد اليومية'),
('notification_email', 'admin@example.com', 'البريد الإلكتروني للإشعارات'),
('working_hours_start', '09:00', 'وقت بدء ساعات العمل'),
('working_hours_end', '17:00', 'وقت نهاية ساعات العمل'),
('maintenance_mode', '0', 'وضع الصيانة (0: مغلق، 1: مفتوح)');