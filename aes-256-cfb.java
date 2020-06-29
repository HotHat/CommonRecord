import kotlin.text.Charsets;

import javax.crypto.Cipher;
import javax.crypto.SecretKey;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;
import java.security.MessageDigest;
import java.util.Arrays;
import java.util.Base64;

public class Bbb {

    public void printBytes(byte[] bytes) {
        for (byte b : bytes) {
            System.out.format("%02x-", b);
        }
        System.out.println();
    }

    public static String encrypt(String strToEncrypt, String secret)
    {
        try
        {
            byte[] iv = { 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8 };
            IvParameterSpec ivspec = new IvParameterSpec(iv);

            // [B@330bedb4
//            System.out.println(Bbb.bytesToHex(iv));

            SecretKeySpec secretKey = new SecretKeySpec(secret.getBytes(), "AES");

            Cipher cipher = Cipher.getInstance("AES/CFB8/NoPadding");
            cipher.init(Cipher.ENCRYPT_MODE, secretKey, ivspec);
            return Base64.getEncoder().encodeToString(cipher.doFinal(strToEncrypt.getBytes()));
        }
        catch (Exception e)
        {
            System.out.println("Error while encrypting: " + e.toString());
        }
        return null;
    }





//    private String decrypt(String sdata, String sKey) throws Exception {
//
//        byte[] key = sKey.getBytes();
//        byte[] iv = Arrays.copyOfRange(hash("SHA-256", key).getBytes(), 0, 16);
//        for (byte b : iv) {
//            System.out.format("%02X", b);
//        }
//        System.out.println();
//
//
//        byte[] data = Base64.getDecoder().decode(sdata.getBytes());
//
//        Cipher cipher = Cipher.getInstance("AES/CFB8/NoPadding");
//        SecretKey aesSecret = new SecretKeySpec(key, "AES");
//        IvParameterSpec ivps = new IvParameterSpec(iv);
//
//        System.out.println("Key: " + new String(aesSecret.getEncoded()));
////        printBytes(aesSecret.getEncoded());
//        cipher.init(Cipher.DECRYPT_MODE, aesSecret, ivps);
//        byte[] result = cipher.doFinal(data);
//
//        System.out.println("Block size: " +  cipher.getBlockSize());
//        System.out.println("IV: " + new String(cipher.getIV()));
////        printBytes(cipher.getIV());
//
//        return new String(result);
////        StringBuilder sb = new StringBuilder();
////        for (int i = 0; i < result.length; i++) {
////            sb.append(Integer.toString((result[i] & 0xff) + 0x100, 16).substring(1));
////        }
////
////        System.out.println(new String(result, StandardCharsets.UTF_8) + " : " + sb.toString());
////        return sb.toString();
//    }

    public String decrypt2(String cipherText, String password) throws Exception {

        byte[] key = Arrays.copyOfRange(hash("SHA-256", password.getBytes()), 0, 32);


        byte[] cipherBytes = Base64.getDecoder().decode(cipherText.getBytes());
//        System.out.println("IV: ");
//        printBytes(cipherBytes);

        byte[] iv = Arrays.copyOfRange(cipherBytes, 0, 16);

        System.out.println("IV: ");
        printBytes(iv);
        System.out.println("KEY: ");
        printBytes(key);


        byte[] data = Arrays.copyOfRange(cipherBytes, 16, cipherBytes.length);

        Cipher cipher = Cipher.getInstance("AES/CFB/NoPadding");
        SecretKey aesSecret = new SecretKeySpec(key, "AES");
        IvParameterSpec ivps = new IvParameterSpec(iv);

        cipher.init(Cipher.DECRYPT_MODE, aesSecret, ivps);
        byte[] result = cipher.doFinal(data);

        return new String(result);
    }


    private byte[] hash(String type, byte[] data) throws Exception {
        MessageDigest digest = MessageDigest.getInstance(type);
        data = digest.digest(data);
        return data;
//
//        StringBuilder sb = new StringBuilder();
//        for (int i = 0; i < data.length; i++) {
//            sb.append(Integer.toString((data[i] & 0xff) + 0x100, 16).substring(1));
//        }
//
//        return sb.toString();
    }

    public  byte[]  hash3(byte[] password, int type) throws Exception {
        MessageDigest md5 = MessageDigest.getInstance("md5");
        byte[] m = new byte[1024];

        int i = 0;

        int keyIvLength = 32 + 16;
        byte[] dg = {};
        while (i < keyIvLength) {
            byte[] data;
            if (i == 0) {
                data = Arrays.copyOfRange(password,  0,password.length);
            } else {
                data = new byte[dg.length + password.length];
                System.arraycopy(dg,  0, data, 0, dg.length);
                System.arraycopy(password,  0, data, dg.length, password.length);
            }

            md5.update(data);
            dg = md5.digest();
            System.arraycopy(dg, 0, m, i, dg.length);

            i += dg.length;
        }

        // key
        if (type == 1) {
            return Arrays.copyOfRange(m, 0, 32);
        }
        // iv
        else {
            return Arrays.copyOfRange(m, 32, 32 + 16);
        }
    }



    public  String encrypt3(String plainText, String secret)
    {
        try
        {
            byte[] secretBytes = secret.getBytes();
            byte[] iv = hash3(secretBytes, 2);
            byte[] key = hash(secretBytes, 1);
            System.out.println("IV");
            printBytes(iv);
            System.out.println("KEY");
            printBytes(key);


            // [B@330bedb4
//            System.out.println(Bbb.bytesToHex(iv));

            byte[] plainBytes= plainText.getBytes(Charsets.UTF_8);
            System.out.println("plain bytes:");
            printBytes(plainBytes);

            int rem = plainBytes.length % 16;

            int plainBytesLength = plainBytes.length;

            if (rem > 0) {
                int nextLength = plainBytes.length + (16 - rem);
                plainBytes = Arrays.copyOf(plainBytes, nextLength);
                for (int i = plainBytesLength;  i < nextLength; ++i) {
                    plainBytes[i] = (byte)0;
                }
            }

            printBytes(plainBytes);

            IvParameterSpec ivspec = new IvParameterSpec(iv);
            SecretKeySpec secretKey = new SecretKeySpec(key, "AES");
            Cipher cipher = Cipher.getInstance("AES/CFB/NoPadding");
            cipher.init(Cipher.ENCRYPT_MODE, secretKey, ivspec);
            byte[] result = cipher.doFinal(plainBytes);

            System.out.println("result:");
            printBytes(result);

//            byte[] finalBytes = Arrays.copyOfRange(result, 0, secretBytes.length);
            byte[] cipherBytes = new byte[16 + plainBytesLength];
            System.arraycopy(iv, 0, cipherBytes, 0, iv.length);
            System.arraycopy(result, 0, cipherBytes, iv.length, plainBytesLength);

            printBytes(cipherBytes);
            return Base64.getEncoder().encodeToString(cipherBytes);
        }
        catch (Exception e)
        {
            System.out.println("Error while encrypting: " + e.toString());
        }
        return null;
    }
    public String decrypt3(String cipherText, String password) throws Exception {

        byte[] key = hash3(password.getBytes(), 1);


        byte[] cipherBytes = Base64.getDecoder().decode(cipherText.getBytes());
        System.out.println("cipher bytes: ");
        printBytes(cipherBytes);

        byte[] iv = Arrays.copyOfRange(cipherBytes, 0, 16);

        System.out.println("IV: ");
        printBytes(iv);
        System.out.println("KEY: ");
        printBytes(key);


        byte[] data = Arrays.copyOfRange(cipherBytes, 16, cipherBytes.length);

        System.out.println("cipher bytes");
        printBytes(data);
        Cipher cipher = Cipher.getInstance("AES/CFB/NoPadding");
        SecretKey aesSecret = new SecretKeySpec(key, "AES");
        IvParameterSpec ivps = new IvParameterSpec(iv);

        cipher.init(Cipher.DECRYPT_MODE, aesSecret, ivps);
        byte[] result = cipher.doFinal(data);
        System.out.println("decrypt bytes");
        printBytes(result);

        return new String(result);
    }

    public static void main(String[] args) throws Exception {

//        String sKey = "12345678901234567890123456789012";
//
        Bbb b = new Bbb();

//        String a = b.decrypt("U2FsdGVkX1+8xrmiQ0iLD0XOt3HYCe+ba2fZQ2u2JQiZ", sKey);
//        System.out.println(a);
//
//        System.out.println("Padding: " + sKey.length() % 16);

//        byte[] ac = "728fe9d6e55e639cQy9Ldnp5b3lraTRs".getBytes();
//        for (byte ba : ac) {
//            System.out.format("%02X", ba);
//        }
//        System.out.println();


        // T6fSHYkTFg==
        // 37323866653964366535356536333963
//        String encrypt = Bbb.encrypt("abcdefg", "01234567890123456789012345678912");
//        System.out.println(encrypt);

//        System.out.println(b.decrypt2("dz0W/n4d/h3cjIf/1UWPJOdGxOTrSRU5rPt58gzKEvGCPpz8PwU=", "SecretKey123456"));
        byte[] ac = b.hash3("SecretKey".getBytes(), 1);
        b.printBytes(ac);


        String plain = b.decrypt3("4QcYLY4cVkyc2+cOd0H8CvdhpXjuGCHR0ZW19+tjGxGHWcVKuVqy6JxYgUPm+Q==", "SecretKey");
        System.out.println(plain);

        System.out.println("new encrypt");
        String plain2 = b.encrypt3("It's encrypt from shadowsocks!", "SecretKey");
        System.out.println(plain2);
        String plain3 = b.decrypt3(plain2, "SecretKey");
        System.out.println(plain3);

    }
}
