extends Node

var user = false  # onko käyttäjä kirjaantunut

# Lähetetään palvelimelle käyttäjätunnus ja salasana, data palautuu JSON-muodossa. 

@onready var http_request = HTTPRequest.new()

func _ready():
	add_child(http_request)
	http_request.request_completed.connect(self._on_login_received)


func login(data: Dictionary) -> void:
	var url = "http://localhost/godot_test/login.php"
	var body := JSON.stringify(data)
	var headers := ["Content-Type: application/json"]

	# Lähetetään pyyntö http_request-nodella (sen täytyy olla olemassa!)
	var error := http_request.request(url, headers, HTTPClient.METHOD_POST, body)
	if error != OK:
		print("Virhe HTTP-pyynnössä: %s" % error)
	else:
		print("Sending data...")



func _on_login_received(_result, response_code, _headers, body):
	print("Response code:", response_code)
	#virheilmoitus, jos sellainen tulee
	if response_code != 200:
		%LoginInfoLabel.text = "Virhe: HTTP " + str(response_code)
		return
	var text = body.get_string_from_utf8()
	var json = JSON.parse_string(text)
	print("Serverin vastaus:", json)
	
	if json.status == "succeeded":
		%LoginInfoLabel.text = "Kirjaantuminen onnistui"
		user = true
	elif json.status == "failed":
		%LoginInfoLabel.text = "Virheellinen käyttäjätunnus tai salasana"
	else:
		%LoginInfoLabel.text = "Kirjautuminen epäonnistui"


func _on_login_button_pressed():
	%LoginInfoLabel.text = ""
	var username = %UsernameLineEdit.text.strip_edges()
	var password = %PasswordLineEdit.text.strip_edges()
	if username == "":
		%LoginInfoLabel.text = "Käyttäjätunnus ei saa olla tyhjä!"
		return
	if password == "":
		%LoginInfoLabel.text = "Salasana ei saa olla tyhjä!"
		return
	
	login({"username": username, "password": password})
