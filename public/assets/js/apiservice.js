class ApiService {
    constructor(baseURL) {
        this.baseURL = baseURL;
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Error al obtener los datos de:', url, error);
            return null;
        }
    }

    get(endpoint, headers = {}) {
        return this.request(endpoint, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json', ...headers },
        });
    }

    post(endpoint, body, headers = {}) {
        return this.request(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...headers },
            body: JSON.stringify(body),
        });
    }

    put(endpoint, body, headers = {}) {
        return this.request(endpoint, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', ...headers },
            body: JSON.stringify(body),
        });
    }

    delete(endpoint, headers = {}) {
        return this.request(endpoint, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', ...headers },
        });
    }
}

export default ApiService;
