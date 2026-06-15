export const DB_NAME = 'YahyaOfflineSync';
export const STORE_NAME = 'requests';

export function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, 1);
        request.onupgradeneeded = (event) => {
            const db = (event.target as any).result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
            }
        };
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

export async function addRequestToQueue(url: string, method: string, data: any, headers: any = {}) {
    const db: any = await openDB();
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(STORE_NAME, 'readwrite');
        const store = transaction.objectStore(STORE_NAME);
        const req = store.add({
            url,
            method,
            data,
            headers,
            timestamp: Date.now()
        });
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

export async function processQueue() {
    const db: any = await openDB();
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(STORE_NAME, 'readwrite');
        const store = transaction.objectStore(STORE_NAME);
        const getAllRequest = store.getAll();

        getAllRequest.onsuccess = async () => {
            const requests = getAllRequest.result;
            if (requests.length === 0) return resolve(true);

            for (const request of requests) {
                try {
                    // Si data contient des infos csrf, on essaie d'envoyer
                    const fetchOptions: RequestInit = {
                        method: request.method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            ...request.headers
                        },
                        body: JSON.stringify(request.data)
                    };

                    const response = await fetch(request.url, fetchOptions);

                    if (response.ok || response.status === 422) {
                        // Success or validation error (we drop validation error offlines to not block queue)
                        const delTransaction = db.transaction(STORE_NAME, 'readwrite');
                        delTransaction.objectStore(STORE_NAME).delete(request.id);
                    }
                } catch (err) {
                    console.error('Offline sync failed for request', request, err);
                }
            }
            resolve(true);
        };
        getAllRequest.onerror = () => reject(getAllRequest.error);
    });
}
