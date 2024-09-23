<?php

namespace PayPal\Auth\Oauth;

use Override;

/**
 * The HMAC-SHA1 signature method uses the HMAC-SHA1 signature algorithm as defined in [RFC2104]
 * where the Signature Base String is the text and the key is the concatenated values (each first
 * encoded per Parameter Encoding) of the Consumer Secret and Token Secret, separated by an '&'
 * character (ASCII code 38) even if empty.
 *   - Chapter 9.2 ("HMAC-SHA1")
 */
class OAuthSignatureMethodHmacSha1 extends OAuthSignatureMethod
{
    #[Override]
    public function get_name()
    {
        return "HMAC-SHA1";
    }

    #[Override]
    public function build_signature($request, $consumer, $token)
    {
        $base_string          = $request->get_signature_base_string();
        $base_string          = preg_replace_callback(
            "/(%[A-Za-z0-9]{2})/",
            [$this, "replace_callback"],
            $base_string
        );//convert base string to lowercase
        $request->base_string = $base_string;

        $key_parts = [$consumer->secret, ($token) ? $token->secret : ""];

        $key_parts = OAuthUtil::urlencode_rfc3986($key_parts);
        $key       = implode('&', $key_parts);
        $key       = preg_replace_callback(
            "/(%[A-Za-z0-9]{2})/",
            [$this, "replace_callback"],
            $key
        );//convert to lowercase

        return base64_encode(hash_hmac('sha1', $base_string, (string) $key, true));
    }

    /**
     * preg_replace_callback callback function
     *
     * @param mixed $match
     */
    private function replace_callback($match)
    {
        return strtolower((string) $match[0]);
    }
}
