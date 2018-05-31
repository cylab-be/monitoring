package rucd.monitoring.client;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.util.EntityUtils;


public class Uploader {
private String json;
private HttpClient httpclient;
private HttpPost httppost ;
public static String url;

	public Uploader(String json) {
		// TODO Auto-generated constructor stub
		this.json = json;
	}
	public Uploader() {
		
	}
	public void initialize() {
		httpclient = HttpClients.createDefault();
		
	}
	
	public void post() throws Exception {
		if(url==null)url="192.168.0.71";
		String baseUrl = "http://"+url+"/Laravel/public/api/";
		String url = "sensors/";
		httppost = new HttpPost(baseUrl+url);
		List<NameValuePair> params = new ArrayList<NameValuePair>(1);
		params.add(new BasicNameValuePair("content", json));
		params.add(new BasicNameValuePair("token",Main.token));
		httppost.setEntity(new UrlEncodedFormEntity(params, "UTF-8"));
		HttpResponse response = httpclient.execute(httppost);
		HttpEntity entity = response.getEntity();

		if (entity != null) {
		    InputStream instream = entity.getContent();
		    try {
		        instream.toString();
		    } finally {
		        instream.close();
		    }
		}
	}
	public String register() throws Exception{
		if(url==null)url="192.168.0.71";
		String baseUrl = "http://"+url+"/Laravel/public/api/";
		String url = "register";
		HttpGet httpget = new HttpGet(baseUrl+url);
		HttpResponse response = httpclient.execute(httpget);
		    if (response.getStatusLine().getStatusCode() == HttpStatus.SC_OK) {
		        String result = null;
		        HttpEntity entity = response.getEntity();
		        if (entity != null) {
		            result = EntityUtils.toString(entity);
		        }
		        return result;
		    } else if (response.getStatusLine().getStatusCode() == HttpStatus.SC_UNAUTHORIZED) {
		        return "401 SC_UNAUTHORIZED";
		    }
		    return "UNKNOWN ERROR";
	}
}
