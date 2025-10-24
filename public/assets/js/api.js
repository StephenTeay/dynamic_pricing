// public/assets/js/api.js

/**
 * Base API class for handling API requests
 */
class Api {
    static async get(endpoint) {
        try {
            const response = await fetch(BASE_URL + endpoint);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    static async post(endpoint, data) {
        try {
            const response = await fetch(BASE_URL + endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }
}

// Product API endpoints
class ProductApi {
    static async getProduct(id) {
        return await Api.get(`/api/v1/products/${id}`);
    }

    static async getProducts(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return await Api.get(`/api/v1/products?${queryString}`);
    }
}

// Cart API endpoints
class CartApi {
    static async validateStock(productId, quantity) {
        return await Api.post('/api/v1/products/validate-stock', {
            productId,
            quantity
        });
    }
}
