# 🚀 Pocket AI - Financial Health Tracker

*Smart, Private, and Your Gen-Z Financial Bestie.*

## 📋 Overview

Pocket AI is an intelligent financial tracking ecosystem designed to turn financial literacy into a daily, engaging habit. Unlike conventional apps that rely on external cloud AI Agent, Pocket AI is built with a **Privacy-First** philosophy. We leverage a **private, self-hosted AI architecture** to deliver personalized financial advice, ensuring that your sensitive spending data never leaves your controlled environment.

Our AI persona provides casual, *rojak* (mixed Malay/English) financial coaching that resonates with the Malaysian lifestyle. By combining lightweight database logic with localized AI processing, we provide a seamless experience that prioritizes both data sovereignty and user engagement.


## 🌐 Live Demo
You can try the live version of Pocket AI here: 
👉 [Pocket AI Live Portal](https://103.40.207.24/pocket-ai)


## 🛠 Tech Stack

* **AI Engine:** Self-hosted vLLM (Meta-Llama-3-8B-Instruct).
* **Backend:** Python, FastAPI (Deployed on Private Cloud/RunPod).
* **Frontend:** PHP, HTML5, CSS3, JavaScript (Fetch API).
* **Architecture:** Private-hosted, zero third-party API dependency.

## 🚀 How to Setup

### 1. Database Initialization

Ensure you have MySQL/MariaDB installed. Initialize the database using the provided schema:

```bash
mysql -u [username] -p [database_name] < backend/database/pocket_ai.sql

```

### 2. Backend (RunPod/Private Cloud)

* Ensure `vllm` and `fastapi` dependencies are installed.
* Run the server:

```bash
python3 backend/server.py

```

* The API endpoint will be available at your configured proxy URL.

### 3. Frontend

* Host the PHP files on an Apache/Nginx server (e.g., XAMPP).
* Ensure your `fetch()` requests point to your backend API URL:

```javascript
const response = await fetch("API_URL/chat", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ prompt: "..." })
});

```

## 🤖 AI Logic & Integration

Pocket AI acts as an integrated financial buddy. The AI processes your spending data and provides advice in a structured, conversational format.

**Response Structure:**

```text
[Casual financial advice in Gen-Z/rojak style...]

### Financial Summary
- Summary: [Casual insights]

```

## 🔒 Privacy & Security

* **Data Sovereignty:** By using a self-hosted architecture, all user data is processed locally. We rely on no third-party AI providers (like OpenAI/Anthropic), keeping your financial life truly private.
* **Security:** Always use `.gitignore` to prevent sensitive credentials or `.env` files from being pushed to the repository.