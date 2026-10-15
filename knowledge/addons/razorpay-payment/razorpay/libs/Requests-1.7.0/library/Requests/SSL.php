<?php
/**
 * SSL utilities for Requests
 *
 * @package Requests
 * @subpackage Utilities
 */

/**
 * SSL utilities for Requests
 *
 * Collection of utilities for working with and verifying SSL certificates.
 *
 * @package Requests
 * @subpackage Utilities
 */
class Requests_SSL {
	/**
	 * Verify the certificate against common name and subject alternative names
	 *
	 * Unfortunately, PHP doesn't check the certificate against the alternative
	 * names, leading things like 'https://www.github.com/' to be invalid.
	 * Instead
	 *
	 * @see https://tools.ietf.org/html/rfc2818#section-3.1 RFC2818, Section 3.1
	 *
	 * @throws Requests_Exception On not obtaining a match for the host (`fsockopen.ssl.no_match`)
	 * @param string $host Host name to verify against
	 * @param array $cert Certificate data from openssl_x509_parse()
	 * @return bool
	 */
	public static function verify_certificate($host, $cert) {
		
		$parts = explode('.', $host);
		if (ip2long($host) === false) {
			$parts[0] = '*';
		}
		$wildcard = implode('.', $parts);

		$has_dns_alt = false;

		
		if (!empty($cert['extensions']) && !empty($cert['extensions']['subjectAltName'])) {
			$altnames = explode(',', $cert['extensions']['subjectAltName']);
			foreach ($altnames as $altname) {
				$altname =app_trim($altname);
				if (strpos($altname, 'DNS:') !== 0) {
					continue;
				}

				$has_dns_alt = true;

				
				$altname =app_trim(substr($altname, 4));

				
				if (self::match_domain($host, $altname) === true) {
					return true;
				}
			}
		}

		
		
		if (!$has_dns_alt && !empty($cert['subject']['CN'])) {
			
			if (self::match_domain($host, $cert['subject']['CN']) === true) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Verify that a reference name is valid
	 *
	 * Verifies a dNSName for HTTPS usage, (almost) as per Firefox's rules:
	 * - Wildcards can only occur in a name with more than 3 components
	 * - Wildcards can only occur as the last character in the first
	 *   component
	 * - Wildcards may be preceded by additional characters
	 *
	 * We modify these rules to be a bit stricter and only allow the wildcard
	 * character to be the full first component; that is, with the exclusion of
	 * the third rule.
	 *
	 * @param string $reference Reference dNSName
	 * @return boolean Is the name valid?
	 */
	public static function verify_reference_name($reference) {
		$parts = explode('.', $reference);

		
		$first = array_shift($parts);

		if (strpos($first, '*') !== false) {
			
			if ($first !== '*') {
				return false;
			}

			
			if (count($parts) < 2) {
				return false;
			}
		}

		
		foreach ($parts as $part) {
			if (strpos($part, '*') !== false) {
				return false;
			}
		}

		
		return true;
	}

	/**
	 * Match a hostname against a dNSName reference
	 *
	 * @param string $host Requested host
	 * @param string $reference dNSName to match against
	 * @return boolean Does the domain match?
	 */
	public static function match_domain($host, $reference) {
		
		if (self::verify_reference_name($reference) !== true) {
			return false;
		}

		
		if ($host === $reference) {
			return true;
		}

		
		
		
		if (ip2long($host) === false) {
			$parts = explode('.', $host);
			$parts[0] = '*';
			$wildcard = implode('.', $parts);
			if ($wildcard === $reference) {
				return true;
			}
		}

		return false;
	}
}