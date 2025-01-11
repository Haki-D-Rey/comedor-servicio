class ApiService {
  constructor(baseURL) {
    this.baseURL = baseURL;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const response = await fetch(url, options);
    const contentType = response.headers.get("Content-Type");
    try {
      if (!response.ok) {
        throw new Error(`Error en la solicitud: ${response.status}`);
      }

      // Si el tipo de contenido es JSON
      if (contentType && contentType.includes("application/json")) {
        return await response.json();
      }

      // Si el tipo de contenido es un archivo (Blob)
      if (
        contentType &&
        contentType.includes(
          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        )
      ) {
        // Aquí se manejaría un archivo de tipo Excel (.xlsx)
        return await response.blob();
      }

      // Si el tipo de contenido es otro (por ejemplo, imagen)
      if (contentType && contentType.includes("image/")) {
        return await response.blob();
      }

      // En caso de que el tipo de contenido no sea reconocido
      throw new Error(`Tipo de contenido no soportado: ${contentType}`);
    } catch (error) {
      console.error("Error al obtener los datos de:", url, error);

      // Si el tipo de contenido es JSON
      if (contentType && contentType.includes("application/json")) {
        return (await response.json()) || null;
      }

      return (await response) || null;
    }
  }

  get(endpoint, headers = {}) {
    return this.request(endpoint, {
      method: "GET",
      headers: { "Content-Type": "application/json", ...headers },
    });
  }

  post(endpoint, body, headers = {}) {
    return this.request(endpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json", ...headers },
      body: JSON.stringify(body),
    });
  }

  put(endpoint, body, headers = {}) {
    return this.request(endpoint, {
      method: "PUT",
      headers: { "Content-Type": "application/json", ...headers },
      body: JSON.stringify(body),
    });
  }

  delete(endpoint, headers = {}) {
    return this.request(endpoint, {
      method: "DELETE",
      headers: { "Content-Type": "application/json", ...headers },
    });
  }
}

export default ApiService;
