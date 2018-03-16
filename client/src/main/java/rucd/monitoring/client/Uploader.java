package rucd.monitoring.client;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.message.BasicNameValuePair;


public class Uploader {
private String json;
private HttpClient httpclient;
private HttpPost httppost ;
private String url = "http://192.168.0.71/Laravel/public/api/sensors";
	public Uploader(String json) {
		// TODO Auto-generated constructor stub
		this.json = json;
	}
	public void initialize() {
		httpclient = HttpClients.createDefault();
		httppost = new HttpPost(url);
	}
	
	public void post() throws Exception {

		List<NameValuePair> params = new ArrayList<NameValuePair>(1);
		params.add(new BasicNameValuePair("content", json));
		params.add(new BasicNameValuePair("token",Main.token));
		httppost.setEntity(new UrlEncodedFormEntity(params, "UTF-8"));
		HttpResponse response = httpclient.execute(httppost);
		HttpEntity entity = response.getEntity();

		if (entity != null) {
		    InputStream instream = entity.getContent();
		    try {
		        System.out.println(instream.toString());
		    } finally {
		        instream.close();
		    }
		}
	}
}
