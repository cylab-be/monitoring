package rucd.monitoring.client;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStreamReader;

import com.fasterxml.jackson.core.JsonGenerationException;
import com.fasterxml.jackson.databind.JsonMappingException;
import com.fasterxml.jackson.databind.ObjectMapper;

public class Start {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		Monitor mon = new Monitor();
		String ok = mon.executeCommand("df");
		mon.getHardDisksState();
		//mon.getUpdate();
		mon.rebootRequired();
		ObjectMapper mapper = new ObjectMapper();
		try {
			// Convert object to JSON string and pretty print
			String jsonInString = mapper.writerWithDefaultPrettyPrinter().writeValueAsString(mon);
			System.out.println(jsonInString);

		} catch (JsonGenerationException e) {
			e.printStackTrace();
		} catch (JsonMappingException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}


}
