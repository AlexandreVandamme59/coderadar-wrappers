import requests

class CoderadarAPI:
    def __init__(self, token=None):
        self.base_url = 'https://api.coderadar.io'
        self.token = token
        self.headers = {'Content-Type': 'application/json'}
        if self.token:
            self.headers['Authorization'] = f'Bearer {self.token}'

    def make_request(self, method, path, params=None):
        if params is None:
            params = {}
        url = f'{self.base_url}{path}'
        if method.upper() == 'GET':
            response = requests.get(url, headers=self.headers, params=params)
        elif method.upper() == 'POST':
            response = requests.post(url, headers=self.headers, json=params)
        else:
            raise ValueError("Unsupported HTTP method")
        return response.json()

    def get_home(self):
        return self.make_request('GET', '/')

    def search(self, query, group=None, aggregations=False, page=1, size=100):
        params = {
            'query': query,
            'group': group,
            'aggregations': aggregations,
            'page': page,
            'size': size,
        }
        return self.make_request('GET', '/search', params=params)

    def get_me(self):
        if not self.token:
            raise Exception("Token is required for getting user information.")
        return self.make_request('GET', '/me')

# Example usage
if __name__ == '__main__':
    api_token = 'YOUR_API_TOKEN_HERE'  # Use your actual API token here
    coderadar = CoderadarAPI(api_token)

    # Example call to the home endpoint
    home_response = coderadar.get_home()
    print("Home API Response:")
    print(home_response)

    # Example call to the search endpoint (requires authentication)
    try:
        search_response = coderadar.search('a depth:all')
        print("\nSearch API Response:")
        print(search_response)
    except Exception as e:
        print("Error:", e)

    # Example call to the me endpoint (requires authentication)
    try:
        me_response = coderadar.get_me()
        print("\nUser Information:")
        print(me_response)
    except Exception as e:
        print("Error:", e)
