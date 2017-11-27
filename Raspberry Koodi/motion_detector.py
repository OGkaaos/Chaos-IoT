# tämän koodin voit luoda ilman koodin muokkailua: https://marcosraudkett.com/mvrclabs/code/scripts/admin/docs/?do=python-generator
import os                           # Käyttöjärjestelmä
import RPi.GPIO as GPIO             # Raspberry GPIO
import time                         # Ajanhallinta
import requests                     # Nettisivun selailua varten
import picamera                     # Raspberry kamera (PiCamera())
import ftplib                       # FTP Latausta varten
import datetime                     # Päivämäärän luontia varten
import subprocess                   # Remote-Terminal varten
import threading                    # Timer:iä varten
from PIL import Image               # Jotta voidaan vaihtaa kuvan kokoa
from resizeimage import resizeimage # Jotta voidaan vaihtaa kuvan kokoa

camera = picamera.PiCamera() # PiCamera
pin = 18                     # Liiketunnistimen PIN -> 18

GPIO.setmode(GPIO.BCM)       # GTIO BCM Mode (GPIO.BCM ja GPIO.BOARD erot löytyvät: https://raspberrypi.stackexchange.com/questions/12966/what-is-the-difference-between-board-and-bcm-for-gpio-pin-numbering)
GPIO.setup(pin, GPIO.IN)     # GPIO IN

print "Paina Ctrl+C lopetakseen."
time.sleep(2)
print "Valmis..";

# Remote Terminal Koodi (Hakee tietokannasta tai script/pi_shell.php sivulta tekstiä jossa on tietokannassa commander taulukossa on command_read = 0)
# Eli jos script/pi_shell.php sivustolle ilmentyy joku teksti ja se on command_read = '0' niin se suorittaa sen komentona ja päivittää 
# myös script/pi-shell.php tiedostossa command_read = 1 joka ei näy enään sivulla.
# script/pi_shell_response.php hakee tietokannasta komentoja tai teksitä jossa on command_read = 1 ja päivittää siihen vastauksen(response) &resonse=VASTAUS
# jonka jälkeen se päivittää myös command_read = 2 (valmis)
def updates():
    threading.Timer(3.0, updates).start() # timer :: toistuu aina 3 sek välein
    
    # hakee/etsii tulevia komentoja
    link = 'https://marcosraudkett.com/mvrclabs/code/scripts/admin/script/pi-terminal/APPKEY/'
    f = requests.get(link) # lukee sivulla olevan tekstin
    #print f.text
    
    # toteutetaan komento raspberrillä
    shell = subprocess.Popen(f.text, shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT).stdout
    response = shell.read() # luetaan teksti (response)
    #print response
    
    # skripti joka palauttaa viestin äskeiselle komennolla joka toteutettiin
    link_response = 'https://marcosraudkett.com/mvrclabs/code/scripts/admin/script/pi-terminal-response/APPKEY/&response='+response
    f2 = requests.get(link_response) # lukee sivulla olevan tekstin
    
updates()

# Liikettä Havaittu
try:
    while True:
        if (GPIO.input(pin)==1):
            print "Liiketta Havaittu"
            
            # luodaan päivämäärä + kellonaika NYT
            date = datetime.datetime.now().strftime("%m-%d-%Y_%H%M%S")
            
            # ottaa kuvan
            camera.capture('/home/pi/Desktop/havainnot/' + date + '.png')

            # tässä muutetaan kuvan kokoa jotta dashboard toimisi mahdollisimman nopeasti
            fd_img = open('/home/pi/Desktop/havainnot/' + date + '.png', 'r') # Haetaan kuva
            img = Image.open(fd_img) # avataan kuva
            img = resizeimage.resize_cover(img, [480, 360]) # muutetaan kuvan koko 480x360 (leveys x pituus)
            img.save('/home/pi/Desktop/havainnot/' + date + '.png', img.format)  # tallennetaan kuva
            fd_img.close() # suljetaan resizeimage
            
            camera.vflip = True # käänetään kuva

            # FTP Lataus (FTP tunnuksen root kansiona pitää olla applikaation uploads/ kansio ja 777 oikeudet jotta se voi
            # ladata siihen kuvia.
            session = ftplib.FTP('website.com','FTP KÄYTTÄJÄNIMI','FTP SALASANA') # FTP Tiedot
            file = open('/home/pi/Desktop/havainnot/' + date + '.png', 'rb') # Haetaan raspberry:lta kuva /kansio/kansio/-"-/-"-/pvm.png             
            session.storbinary('STOR ' + date + '.png', file) # ladataan kyseinen kuva ftp protokollalla uploads/ kansioon
            file.close() # suljetaan tiedosto.                               
            session.quit() # suljetaan FTP yhteys
            
            # skripti joka tallentaa tietokantaan ja myös ilmoittaa käyttäjille jolla on kyseinen appkey ja &img=KUVAN PVM.png
            requests.get('https://marcosraudkett.com/mvrclabs/code/scripts/admin/script/service.php?app_key=APPKEY&img=' + date + '.png').content
            
            os.remove('/home/pi/Desktop/havainnot/' + date + '.png') # Poistetaan kuva omalta Raspberry:ltä. (säästääkseen tilaa)
        
        # odottaa 4 sek ennenkuin voi havaita uudelleen.
        time.sleep(4)
except KeyboardInterrupt:
    print "Dead" # jos keyboardilla painetaan CTRL+C niin ohjelma lopetetaan.
    GPIO.cleanup()
