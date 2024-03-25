<?php

class CoderadarAPI {
    protected $baseUrl = 'https://api.coderadar.io';
    protected $token = null;

    public function __construct($token = null) {
        $this->token = $token;
    }

    protected function makeRequest($method, $path, $params = []) {
        $url = $this->baseUrl . $path;
        $headers = ['Content-Type: application/json'];

        if ($this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        } elseif ($method === 'GET' && !empty($params)) {
            $url = sprintf("%s?%s", $url, http_build_query($params));
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getHome() {
        return $this->makeRequest('GET', '/');
    }

    public function search($query, $group = null, $aggregations = false, $page = 1, $size = 100) {
        $params = compact('query', 'group', 'aggregations', 'page', 'size');
        return $this->makeRequest('GET', '/search', $params);
    }

    public function getMe() {
        if (!$this->token) {
            throw new Exception("Token is required for getting user information.");
        }
        return $this->makeRequest('GET', '/me');
    }
}

// Example usage
$apiToken = 'YOUR_API_TOKEN_HERE'; 
$coderadar = new CoderadarAPI($apiToken);

// Example call to the home endpoint
$homeResponse = $coderadar->getHome();
echo "Home API Response:\n";
print_r($homeResponse);

// Example call to the search endpoint (requires authentication)
try {
    $searchResponse = $coderadar->search('a depth:all');
    echo "\nSearch API Response:\n";
    print_r($searchResponse);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Example call to the me endpoint (requires authentication)
try {
    $meResponse = $coderadar->getMe();
    echo "\nUser Information:\n";
    print_r($meResponse);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
