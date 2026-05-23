# 🚀 Pocket AI - Financial Tracker

*Your Gen-Z Financial Bestie.*

## 📋 Overview

This project integrates an **AI Backend** (RunPod) with a **Frontend Dashboard** (PHP/JS) to help users track expenses with financial advice that is casual, *rojak* (mixed Malay/English), and has "main character energy."

## 🛠 Tech Stack

* **Backend:** Python, FastAPI, vLLM (Meta-Llama-3-8B-Instruct).
* **Frontend:** PHP, HTML5, CSS3, JavaScript (Fetch API).
* **AI Persona:** "Financial Bestie" (Malaysian Gen-Z slang).

## 🚀 How to Setup

### 1. Backend (RunPod/Local)

* Ensure `vllm` and `fastapi` are installed.
* Run the server:
```bash
python3 server.py

```


* The API endpoint will be available at: `http://localhost:8000/chat` (or your RunPod proxy URL).

### 2. Frontend

* Ensure your `.php` files are running on your web server (Apache/Nginx).
* Use the JavaScript `fetch()` API to send data to the backend:
```javascript
const response = await fetch("YOUR_API_URL/chat", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ prompt: "Your user prompt here" })
});

```



## 🤖 AI Logic

The AI processes natural language input and provides a response in the following format:

```text
[Casual financial advice...]

### Current Financial Ring
Summary: [Short summary]
Data: {"item": "item_name", "amount": number}

```

## ⚠️ Important Note

* Do **not** push `.env` files or any API keys to this repository.
* Use `.gitignore` to prevent cache files or sensitive data from being uploaded.