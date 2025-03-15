# Yii2 GoToWebinar Integration App

## 📌 Overview
This is a **Yii2-based web application** that integrates with the **GoToWebinar API** to manage webinars.  
The app allows users to **create, update, delete, and view webinars** using the GoToWebinar platform.

## ✨ Features
- ✅ **Create new webinars**
- ✏️ **Update existing webinars**
- ❌ **Delete webinars from GoToWebinar and the database**
- 📅 **Manage webinar schedules**

## 🛠️ Installation

### 📌 Prerequisites
- PHP 7.4+
- Composer
- Yii2 Framework
- MySQL
- GoToWebinar API credentials

### 🚀 Setup Steps

1. **Clone the repository**  
   ```sh
   git clone https://github.com/your-repo/yii2-webinar-app.git
   cd webinar-app
   ```
2. **Install dependencies**  
   ```sh
   composer install
   ```
3. **Configure environment**  
   - Configure `config/params.php` with the correct credentials.
   - Configure `config/db.php` with database settings.
4. **Create database locally**  
   - Find the sql file on `config/create_webinar_table.sql`
5. **Start the server**  
   ```sh
   php yii serve --port=8080
   ```
6. **Access the application**  
   Open `http://localhost:8080` in your browser.

## 🔗 API Integration
This app communicates with the **GoToWebinar API** for webinar management.

- **Authentication:** Uses OAuth 2.0 for secure access.
- **Endpoints used:**  
  - `POST /oauth/v2/token` (Refresh Access Token)  
  - `POST /G2W/rest/v2/organizers/{organizer_key}/webinars` (Create Webinar)  
  - `PUT /G2W/rest/v2/organizers/{organizer_key}/webinars/{webinar_key}` (Update Webinar)  
  - `DELETE /G2W/rest/v2/organizers/{organizer_key}/webinars/{webinar_key}` (Delete Webinar)  

## 📝 Usage

### ▶️ Creating a Webinar
Navigate to the homepage and click the **"Create New Webinar"** button.

### ✏️ Updating a Webinar
Select a webinar from the list and click **Edit**.

### ❌ Deleting a Webinar
Click **Delete** on a webinar (requires confirmation).

## 🛠 Troubleshooting

- **Access token expired?** The system attempts to refresh it automatically.
- **Webinar not found?** Ensure the correct webinar key is stored in the database.
