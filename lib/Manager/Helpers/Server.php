<?php
/**
 * @file
 * Abstraction of server. Designed to be easily overridden
 * for testing purposes.
 */

namespace Manager\Helpers;


class Server {
	/**
	 * Property get magic method
	 * @param string $key Property name
	 *
	 * Properties supported:
	 *
	 *
	 * Notice: These are read-only, they cannot be written.
	 *
	 * @return null|array
	 */
	public function __get($key) {
		switch($key) {
			case 'post':
				if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE'])) {
					if($_SERVER['CONTENT_TYPE'] === 'application/x-www-form-urlencoded' ||
						substr($_SERVER['CONTENT_TYPE'], 0, 19) === 'multipart/form-data') {
						return $_POST;
					} else {
						return json_decode(file_get_contents("php://input"), true);
					}
				} else {
				    return $_POST;
                }

			case 'get':
				return $_GET;

			case 'server':
				return $_SERVER;

			case 'session':
				return $_SESSION;

			case 'cookie':
				return $_COOKIE;

			case 'files':
				return $_FILES;

            case 'jwt':
                return $this->getBearerToken();

			default:
				$trace = debug_backtrace();
				trigger_error(
					'Undefined property ' . $key .
					' in ' . $trace[0]['file'] .
					' on line ' . $trace[0]['line'],
					E_USER_NOTICE);
				return null;
		}
	}

    /**
     * Returns the time of the request
     * @return int
     */
	public static function getRequestTime()
    {
        return $_SERVER['REQUEST_TIME'];
    }

	/**
	 * Ensure all required keys are present
	 * @param $list array List to check
	 * @param $required array Array of keys (as values)
	 * @return bool True if all present
	 */
	public static function ensureKeys($list, $required) {
		foreach($required as $require) {
			if(!isset($list[$require])) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Set in the session
	 * @param $key
	 * @param $value
	 */
	public function setSession($key, $value) {
		$_SESSION[$key] = $value;
	}

	public function session_name($name) {
		session_name($name);
	}

	public function session_start() {
		session_start();
	}

	public function redirect($where) {
		//echo "<a href=\"$where\">$where</a>";
		header("location: " . $where);
		exit;
	}

	public function header($value) {
		header($value);
	}

	public function deleteCookie($name) {
		setcookie ($name, "", 1, "/");
		setcookie ($name, false);
	}

	public function setcookie($name, $value, $expire) {
		setcookie($name, $value, $expire, '/', '', false, false);
	}

	public function getRequestIP() {
	    return $_SERVER['REMOTE_ADDR'];
    }

	/**
	 * Parse the request URI into components after the specified parent directory.
	 *
	 * Calling this function when the URI is /whatever/api/user/login and with
	 * $parent set to 'api' will return the array: ['user', 'login'].
	 * @param string $parent Parent directory
	 * @return array|null Array or null if failure.
	 */
	public function parseRequestURI($parent) {
		$uri = $this->__get('server')['REQUEST_URI'];
		$path = explode('/', parse_url($uri, PHP_URL_PATH));

		for($i=0; $i<(count($path) - 1); $i++) {
			if($path[$i] === $parent) {
				$i++;
				break;
			}
		}

		if($i >= count($path) || $path[$i] === '') {
			return null;
		}

		return array_slice($path, $i);
	}

    /**
     * Get header Authorization
     */
    private function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
     * Get access token from header
     */
    public function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

}