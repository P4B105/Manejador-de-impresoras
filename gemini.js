import { GoogleGenAI } from "@google/genai";

const ai = new GoogleGenAI({ apiKey: "AIzaSyAHY6R8m-CyR6LgyPs7Wzd1I74CtPDQ1s4" });

async function gemini() {
  const response = await ai.models.generateContent({
    model: "gemini-2.5-flash",
    contents: "Di hola, gemini",
  });
  console.log(response.text);
}
