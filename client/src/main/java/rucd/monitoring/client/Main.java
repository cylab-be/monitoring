/*
 * The MIT License
 *
 * Copyright 2017 Thibault Debatty.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
package rucd.monitoring.client;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;

import com.fasterxml.jackson.core.JsonGenerationException;
import com.fasterxml.jackson.databind.JsonMappingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import java.util.Map;
import java.util.Properties;
import java.util.Scanner;

public class Main {
	public static String token;
    public static void main(String[] args) {
		Properties properties = new Properties();
    	if(new File("config.properties").isFile()) {
    		InputStream input = null;
    		try {

    			input = new FileInputStream("config.properties");

    			// load a properties file
    			properties.load(input);

    			// get the property value and print it out
    			token = properties.getProperty("token");
    			System.out.println(token);
    		} catch (IOException ex) {
    			ex.printStackTrace();
    		} finally {
    			if (input != null) {
    				try {
    					input.close();
    				} catch (IOException e) {
    					e.printStackTrace();
    				}
    			}
    		}
    	}else{
			System.out.println("Veuillez entrer le token fourni par l'interface d'administration");
			Scanner s = new Scanner(System.in);
			token = s.nextLine();
			
			properties.setProperty("token", token);
			// Save the grades properties using store() and an output stream
			FileOutputStream out;
			try {
				out = new FileOutputStream(
						"config.properties");
				properties.store(out, null);
				out.close();
				System.out.println("Fichier de configuration créer");
			} catch (FileNotFoundException e1) {
				// TODO Auto-generated catch block
				e1.printStackTrace();
			} catch (IOException e1) {
				// TODO Auto-generated catch block
				e1.printStackTrace();
			}
    	}
    	Monitor monitor = new Monitor();
        Map<String, Object> analyze_result = monitor.analyze();
        ObjectMapper mapper = new ObjectMapper();
        try {
            // Convert object to JSON string and pretty print
            String json_string = mapper.writerWithDefaultPrettyPrinter()
                    .writeValueAsString(analyze_result);
            Uploader up = new Uploader(json_string);
            up.initialize();
            try {
				up.post();
			} catch (Exception e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}

        } catch (JsonGenerationException e) {
            e.printStackTrace();
        } catch (JsonMappingException e) {
            e.printStackTrace();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

}
