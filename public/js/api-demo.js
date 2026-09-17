const API_BASE = 'http://127.0.0.1:8000/api';
let token = localStorage.getItem('inclusif_token') || '';

const output = document.getElementById('output');
const tokenInfo = document.getElementById('tokenInfo');

function setOutput(data) {
  output.textContent = JSON.stringify(data, null, 2);
}

function updateTokenInfo() {
  tokenInfo.textContent = token ? `Token : ${token.substring(0, 20)}...` : 'Token : aucun';
}

async function request(path, method = 'GET', body = null) {
  const headers = { Accept: 'application/json' };

  if (body) {
    headers['Content-Type'] = 'application/json';
  }

  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  const response = await fetch(`${API_BASE}${path}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : null,
  });

  const data = await response.json().catch(() => ({}));

  if (!response.ok) {
    throw new Error(JSON.stringify({ status: response.status, data }, null, 2));
  }

  return data;
}

document.getElementById('loginForm').addEventListener('submit', async (event) => {
  event.preventDefault();

  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;

  try {
    const result = await request('/auth/login', 'POST', { email, password });
    token = result.token;
    localStorage.setItem('inclusif_token', token);
    updateTokenInfo();
    setOutput(result);
  } catch (error) {
    setOutput({ error: error.message });
  }
});

document.getElementById('loadCategoriesBtn').addEventListener('click', async () => {
  try {
    const result = await request('/categories');
    setOutput(result);
  } catch (error) {
    setOutput({ error: error.message });
  }
});

document.getElementById('loadDocumentsBtn').addEventListener('click', async () => {
  try {
    const result = await request('/documents');
    setOutput(result);
  } catch (error) {
    setOutput({ error: error.message });
  }
});

document.getElementById('meBtn').addEventListener('click', async () => {
  try {
    const result = await request('/me');
    setOutput(result);
  } catch (error) {
    setOutput({ error: error.message });
  }
});

updateTokenInfo();
