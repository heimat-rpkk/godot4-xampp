extends Node
# Lähetetään palvelimelle käyttäjätunnus ja salasana, koko nini ja email. data palautuu JSON-muodossa. 


@onready var http_request = HTTPRequest.new()
@onready 	var new_scene = load("res://game_secret.tscn") as PackedScene

func _ready():
	add_child(http_request)
	http_request.request_completed.connect(self._on_signin_received)


func signin(data: Dictionary) -> void:
	var url = "http://localhost/godot_test/signin_secret.php"
	var body := JSON.stringify(data)
	var headers := ["Content-Type: application/json"]

	# Lähetetään pyyntö http_request-nodella (sen täytyy olla olemassa!)
	var error := http_request.request(url, headers, HTTPClient.METHOD_POST, body)
	if error != OK:
		print("Virhe HTTP-pyynnössä: %s" % error)
	else:
		print("Sending data...")



func _on_signin_received(_result, response_code, _headers, body):
	print("Response code:", response_code)
	if response_code == 200:
		var text = body.get_string_from_utf8()
		var data = JSON.parse_string(text)
		print("Serverin vastaus:", data)
		if "error" in data:
			%SigninInfoLabel.text = "Virhe: " + str(data["error"])
		elif "success" in data and data["success"] == true:
			Global.user_id = data["id"]  # uusi lisätty käyttäjä
			Global.username = data["username"]  # uusi lisätty käyttäjä
			%SigninInfoLabel.text = "Kirjaantuminen onnistui"
			Global.user = true
			get_tree().change_scene_to_packed(new_scene)
		else:
			%SigninInfoLabel.text = "Tuntematon vastaus palvelimelta"
	else:
		%SigninInfoLabel.text = "HTTP-virhe " + str(response_code)


func _on_signin_button_pressed():
	%SigninInfoLabel.text = ""
	var username = %SigninUsernameLineEdit.text.strip_edges()
	var fullname = %FullnameLineEdit.text.strip_edges()
	var email = %EmailLineEdit.text.strip_edges()
	var passwd = %SigninPasswordLineEdit.text.strip_edges()
	if username == "":
		%SigninInfoLabel.text = "Käyttäjätunnus ei saa olla tyhjä!"
		return
	if fullname == "":
		%SigninInfoLabel.text = "Koko nimi ei saa olla tyhjä!"
		return
	if email == "":
		%SigninInfoLabel.text = "Email ei saa olla tyhjä!"
		return
	if passwd == "":
		%SigninInfoLabel.text = "Salasana ei saa olla tyhjä!"
		return
	
	signin({"username": username, "fullname": fullname, "email": email, "passwd": passwd})
