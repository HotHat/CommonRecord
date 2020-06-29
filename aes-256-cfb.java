import javax.crypto.Cipher;
import javax.crypto.SecretKey;
import javax.crypto.SecretKeyFactory;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.PBEKeySpec;
import javax.crypto.spec.SecretKeySpec;
import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import java.security.spec.KeySpec;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Base64;
import java.util.List;

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

    public  byte[]  hash2(byte[] password) throws Exception {
        MessageDigest md5 = MessageDigest.getInstance("md5");
        byte[] m = new byte[1024];

        int i = 0;

        while (i < 32) {
            byte[] data;
            if (i == 0) {
                data = Arrays.copyOfRange(password,  0,password.length);
            } else {
                data = new byte[i + password.length];

                System.arraycopy(m,  0, data, 0, i);
                System.arraycopy(password,  0, data, i, password.length);

            }
            md5.update(data);
            byte[] dg = md5.digest();
            System.arraycopy(dg, 0, m, i, dg.length);

            i += dg.length;
        }

        byte[] key = Arrays.copyOfRange(m, 0, 32);
        return key;
    }

    public String decrypt3(String cipherText, String password) throws Exception {

        byte[] key = hash2(password.getBytes());


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
        byte[] ac = b.hash2("SecretKey".getBytes());
        b.printBytes(ac);


        String plain = b.decrypt3("MjCuBdSt30z1DchOb7kPSZRv0q+yEbdUqgVbjscrInCzPThRL/AxevLMgqVyrQ==", "SecretKey");
        System.out.println(plain);

    }
}
