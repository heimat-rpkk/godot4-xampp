extends Node

# Tallennus tietokantaan palvelimen kautta. 
# tieto välitetään palvelimelle 
# lomakedatana (x-www-form-urlencoded).

@onready var http_request: HTTPRequest = HTTPRequest.new()

func _ready():
	# Create an HTTP request node and connect its completion signal.
	add_child(http_request)
	http_request.request_completed.connect(self._on_request_completed)



func save(username, score):
	# tiedon muodostaminen tietokantaan tallennusta varten
	var url = "http://localhost/godot_test/save_formdata.php"
	var headers = ["Content-Type: application/x-www-form-urlencoded"]
	#muuttuja=%s, jos merkkijono, muuttuja= %i, jos luku
	#username ja score vastaa php-tiedoston $_POST[‘username’] ja $_POST[‘score’]
	var body = "username=%s&score=%s" % [username.uri_encode(),score.uri_encode()]
	var err = http_request.request(url, headers, HTTPClient.METHOD_POST, body)
	if err != OK:
		print("Error sending request: ", err)
	else:
		print("Sending data...")


# Called when the HTTP request is completed.
func _on_request_completed(_result, _response_code, _headers, body):
	var response_text = body.get_string_from_utf8()
	print("Response: ", response_text)
	match response_text:
		"inserted":
			print("New player added with score.")
			#$Control/Nimi/Pisteet.text = "Tallennettu onnistuneesti"
		"updated":
			print("Score updated! New high score.")
			#$Control/Nimi/Pisteet.text = "Uusi ennättys tallennettu onnistuneesti"
		"not_updated":
			print("Score was lower or equal. Not saved.")
			#$Control/Nimi/Pisteet.text = "Liian pieni tulos"
		_:
			print("Unexpected response: ", response_text)


func _on_save_button_pressed():
	%InfoLabel.text = ""
	var username = %NameLineEdit.text.strip_edges()
	var score = %ScoreLineEdit.text.strip_edges()
	if username == "":
		%InfoLabel.text = "Nimi ei saa olla tyhjä!"
		return
	if not score.is_valid_int():
		%InfoLabel.text = "Pisteet tulee olla numeromuodossa!"
		return
		
	save(username, score)
